<?php

namespace App\Http\Controllers\Payroll;

use App\Models\PayoutChannel;
use App\Http\Requests\StorePayoutChannelRequest;
use App\Http\Requests\UpdatePayoutChannelRequest;
use App\Lib\Enumerations\PayoutChannelType;
use App\Http\Controllers\Controller;
use App\Models\EmployeePayoutChannel;

class PayoutChannelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $results = PayoutChannel::with('employees')->get();

        return view('admin.payroll.payoutChannels.index', ['results'=>$results]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $payoutChannelTypes = [
            'SACCO' => PayoutChannelType::$SACCO,
            'BANK' => PayoutChannelType::$BANK,
            'OTHER' => PayoutChannelType::$OTHER,
        ];
        return view('admin.payroll.payoutChannels.edit', compact('payoutChannelTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePayoutChannelRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePayoutChannelRequest $request)
    {
        $input = $request->all();

        try{
            PayoutChannel::create($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
            \Log::error($e);
        }

        if($bug==0){
            return redirect()->route('payoutChannel.index')->with('success', 'Deduction Successfully saved.');
        }else {
            return back()->with('error', 'Some Error Found !, Please try again.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PayoutChannel  $payoutChannel
     * @return \Illuminate\Http\Response
     */
    public function show(PayoutChannel $payoutChannel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PayoutChannel  $payoutChannel
     * @return \Illuminate\Http\Response
     */
    public function edit($payoutChannel)
    {
        $editModeData = PayoutChannel::findOrFail($payoutChannel);
        $payoutChannelTypes = [
            'SACCO' => PayoutChannelType::$SACCO,
            'BANK' => PayoutChannelType::$BANK,
            'OTHER' => PayoutChannelType::$OTHER,
        ];
        return view('admin.payroll.payoutChannels.edit',['editModeData' => $editModeData], compact('payoutChannelTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePayoutChannelRequest  $request
     * @param  \App\Models\PayoutChannel  $payoutChannel
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePayoutChannelRequest $request, $payoutChannel)
    {
        $data = PayoutChannel::FindOrFail($payoutChannel);
        $input = $request->all();
        try{
            $data->update($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if($bug==0){
            return redirect()->route('payoutChannel.index')->with('success', 'Deduction Successfully Updated.');
        }else {
            return redirect()->back()->with('error', 'Some Error Found !, Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PayoutChannel  $payoutChannel
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $data = PayoutChannel::FindOrFail($id);
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if($bug==0){
            echo "success";
        }elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }


    public function destroyEmpliyeePayoutChannel($id) 
    {
        try{
            $data = EmployeePayoutChannel::FindOrFail($id);
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->errorInfo[1];
        }

        if($bug==0){
            echo "success";
        }elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }
}
