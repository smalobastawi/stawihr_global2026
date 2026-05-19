<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\ApprovalWorkflow;
use App\Models\ApprovalStep;
use App\Models\ApprovalAssignment;
use App\Models\ApprovalLog;
use App\Traits\HasApprovalWorkflow;
use App\Lib\Enumerations\ApprovalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

// Test model class using the trait
class TestApprovalModel extends Model
{
    use HasApprovalWorkflow;

    protected $table = 'test_approval_models';
    protected $fillable = ['name', 'status', 'approval_status', 'created_by', 'date_approved'];

    public function submitter()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

class ApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $testModel;
    protected $workflow;
    protected $users;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test table
        Schema::create('test_approval_models', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('status')->default('draft');
            $table->string('approval_status')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamp('date_approved')->nullable();
            $table->timestamps();
        });

        // Create test users
        $this->users = [
            'submitter' => User::factory()->create(['name' => 'John Submitter']),
            'reviewer1' => User::factory()->create(['name' => 'Jane Reviewer 1']),
            'reviewer2' => User::factory()->create(['name' => 'Bob Reviewer 2']),
            'approver1' => User::factory()->create(['name' => 'Alice Approver 1']),
            'approver2' => User::factory()->create(['name' => 'Charlie Approver 2'])
        ];

        // Create test model
        $this->testModel = new TestApprovalModel();
        $this->testModel->name = 'Test Item';
        $this->testModel->created_by = $this->users['submitter']->id;
        $this->testModel->save();

        // Create approval workflow
        $this->workflow = ApprovalWorkflow::create([
            'model_type' => TestApprovalModel::class,
            'reviewer_config' => ['levels' => 2, 'required_levels' => 2],
            'approver_config' => ['levels' => 2, 'required_levels' => 2],
            'is_active' => true
        ]);

        // Create approval steps manually for testing
        $reviewerStep1 = ApprovalStep::create([
            'approval_workflow_id' => $this->workflow->id,
            'type' => 'reviewer',
            'level' => 1,
            'name' => 'Reviewer 1',
            'is_required' => true
        ]);

        $reviewerStep2 = ApprovalStep::create([
            'approval_workflow_id' => $this->workflow->id,
            'type' => 'reviewer',
            'level' => 2,
            'name' => 'Reviewer 2',
            'is_required' => true
        ]);

        $approverStep1 = ApprovalStep::create([
            'approval_workflow_id' => $this->workflow->id,
            'type' => 'approver',
            'level' => 1,
            'name' => 'Approver 1',
            'is_required' => true
        ]);

        $approverStep2 = ApprovalStep::create([
            'approval_workflow_id' => $this->workflow->id,
            'type' => 'approver',
            'level' => 2,
            'name' => 'Approver 2',
            'is_required' => true
        ]);

        // Create assignments
        ApprovalAssignment::create([
            'approval_step_id' => $reviewerStep1->id,
            'user_id' => $this->users['reviewer1']->id
        ]);

        ApprovalAssignment::create([
            'approval_step_id' => $reviewerStep2->id,
            'user_id' => $this->users['reviewer2']->id
        ]);

        ApprovalAssignment::create([
            'approval_step_id' => $approverStep1->id,
            'user_id' => $this->users['approver1']->id
        ]);

        ApprovalAssignment::create([
            'approval_step_id' => $approverStep2->id,
            'user_id' => $this->users['approver2']->id
        ]);
    }

    /** @test */
    public function it_creates_logs_for_all_approval_levels_when_submitted()
    {
        // Act as submitter
        $this->actingAs($this->users['submitter']);

        // Submit for approval
        $this->testModel->submitForApproval();

        // Assert: Check that logs were created for all steps
        $logs = ApprovalLog::where('approvable_type', TestApprovalModel::class)
            ->where('approvable_id', $this->testModel->id)
            ->get();

        // Should have logs for: 1 submitted + 4 approval steps (1 pending + 3 queued)
        $this->assertCount(5, $logs);

        // Check submission log
        $submittedLog = $logs->where('action', 'submitted')->first();
        $this->assertNotNull($submittedLog);
        $this->assertEquals($this->users['submitter']->id, $submittedLog->user_id);

        // Check pending logs (first step only)
        $pendingLogs = $logs->where('action', 'pending');
        $this->assertCount(1, $pendingLogs);
        
        $firstPendingLog = $pendingLogs->first();
        $firstStep = $this->workflow->steps()->orderBy('type')->orderBy('level')->first();
        $this->assertEquals($firstStep->id, $firstPendingLog->approval_step_id);

        // Check queued logs (remaining steps)
        $queuedLogs = $logs->where('action', 'queued');
        $this->assertCount(3, $queuedLogs);
    }

    /** @test */
    public function it_maintains_sequential_approval_workflow()
    {
        // Submit for approval
        $this->actingAs($this->users['submitter']);
        $this->testModel->submitForApproval();

        // Get current step (should be first reviewer)
        $currentStep = $this->testModel->currentApprovalStep();
        $this->assertEquals('reviewer', $currentStep->type);
        $this->assertEquals(1, $currentStep->level);

        // First reviewer approves
        $this->actingAs($this->users['reviewer1']);
        $result = $this->testModel->processApproval('reviewed', 'First review completed');
        $this->assertTrue($result);

        // Check that next step is now pending
        $currentStep = $this->testModel->currentApprovalStep();
        $this->assertEquals('reviewer', $currentStep->type);
        $this->assertEquals(2, $currentStep->level);

        // Verify that step 2 log was converted from queued to pending
        $step2Log = ApprovalLog::where('approvable_type', TestApprovalModel::class)
            ->where('approvable_id', $this->testModel->id)
            ->where('approval_step_id', $currentStep->id)
            ->where('action', 'pending')
            ->first();
        $this->assertNotNull($step2Log);
    }

    /** @test */
    public function it_completes_full_approval_workflow()
    {
        // Submit for approval
        $this->actingAs($this->users['submitter']);
        $this->testModel->submitForApproval();

        // Complete all approval steps sequentially
        $steps = $this->workflow->steps()->orderBy('type')->orderBy('level')->get();

        foreach ($steps as $index => $step) {
            $user = match($step->type . $step->level) {
                'reviewer1' => $this->users['reviewer1'],
                'reviewer2' => $this->users['reviewer2'],
                'approver1' => $this->users['approver1'],
                'approver2' => $this->users['approver2'],
            };

            $this->actingAs($user);
            $action = $step->type === 'reviewer' ? 'reviewed' : 'approved';
            
            $result = $this->testModel->processApproval($action, "Step {$step->level} {$step->type} completed");
            $this->assertTrue($result);

            // Refresh model
            $this->testModel->refresh();
        }

        // Assert final approval
        $this->assertTrue($this->testModel->isFullyApproved());
        $this->assertEquals(ApprovalStatus::APPROVED, $this->testModel->approval_status);
        $this->assertNotNull($this->testModel->date_approved);
    }

    /** @test */
    public function it_handles_rejection_properly()
    {
        // Submit for approval
        $this->actingAs($this->users['submitter']);
        $this->testModel->submitForApproval();

        // First reviewer rejects
        $this->actingAs($this->users['reviewer1']);
        $result = $this->testModel->processApproval('rejected', 'Not meeting requirements');
        $this->assertTrue($result);

        // Refresh model
        $this->testModel->refresh();

        // Assert rejection
        $this->assertFalse($this->testModel->isFullyApproved());
        $this->assertEquals('inactive', $this->testModel->status);

        // Verify rejection log was created
        $rejectionLog = ApprovalLog::where('approvable_type', TestApprovalModel::class)
            ->where('approvable_id', $this->testModel->id)
            ->where('action', 'rejected')
            ->first();
        $this->assertNotNull($rejectionLog);
        $this->assertEquals('Not meeting requirements', $rejectionLog->comments);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('test_approval_models');
        parent::tearDown();
    }
}