 <div id="area_select" class="col-md-10 col-sm-12" style="margin-top: 20px;">
     <table class="table table-bordered table-striped">
         <thead>
             <tr>
                 <th>Module</th>
                 <th>Menu</th>
                 <th>Section</th>
                 <th>Permissions</th>
             </tr>
         </thead>
         <tbody>
             @foreach ($modules as $module)
                 <?php
                 $menu_id = 'mn__' . str_replace(' ', '_', $module->name);
                 $subSectionCount = $module->permissionGroups()->select('module_id', 'permission_group', 'sub_section', 'sub_section_description')->distinct('module_id', 'permission_group', 'sub_section', 'sub_section_description')->groupBy('module_id', 'permission_group', 'sub_section', 'sub_section_description')->get()->count();
                 // dd($subSectionCount);
                 $tracksubSectionCount = 0;
                 $tracksubSectionCount2 = 0;
                 ?>
                 <tr>
                     <td rowspan="{{ max(1, $subSectionCount) }}">
                         <!-- Menu Name with Checkbox -->
                         <div class="checkbox checkbox-info">
                             <input class="inputCheckbox menucls" type="checkbox" id="menucls__{{ $menu_id }}"
                                 @checked(in_array('pmMenu__' . $module->name, $role_permissions)) name="menu[]" value="pmMenu__{{ $module->name }}">
                             <label for="menuCheckbox{{ $menu_id }}" style="vertical-align: middle;">
                                 <strong>{{ $module->name }}</strong>
                             </label>
                         </div>
                     </td>
                     @php $firstGroup = true;

                     @endphp
                     @foreach ($module->permissionGroups()->select('module_id', 'permission_group', 'group_description')->distinct('module_id', 'permission_group', 'group_description')->groupBy('module_id', 'permission_group', 'group_description')->get() as $pmg)
                         <?php $permission_group_id = 'module_id__' . $module->id . '__pmGroup__' . $pmg->permission_group;
                         $subSectionCount2 = $module
                             ->permissionGroups()
                             ->select('module_id', 'permission_group', 'sub_section', 'sub_section_description')
                             ->where('permission_group', $pmg->permission_group)
                             ->where('module_id', $module->id)
                             ->distinct('module_id', 'permission_group', 'sub_section', 'sub_section_description')
                             ->groupBy('module_id', 'permission_group', 'sub_section', 'sub_section_description')
                             ->get()
                             ->count();
                         $tracksubSectionCount2 += $subSectionCount2;
                         
                         ?>
                         @if (!$firstGroup)
                 <tr>
             @endif
             <td rowspan="{{ max(1, $subSectionCount2) }}">
                 <!-- Permission Group with Checkbox -->
                 <div class="checkbox checkbox-info">
                     <input class="inputCheckbox menucls__{{ $menu_id }} pmgcls" type="checkbox"
                         id="pmgcls__{{ $permission_group_id }}" @checked(in_array($permission_group_id, $role_permissions)) name="permission_group[]"
                         value="{{ $permission_group_id }}" data-group-id="{{ $permission_group_id }}">
                     <label for="groupCheckbox{{ $permission_group_id }}" style="vertical-align: middle;">
                         {{ $pmg->group_description }}
                     </label>
                 </div>
             </td>
             @php $secondGroup = true; @endphp
             @foreach ($module->permissionGroups()->select('module_id', 'permission_group', 'sub_section', 'sub_section_description')->where('permission_group', $pmg->permission_group)->where('module_id', $module->id)->distinct('module_id', 'permission_group', 'sub_section', 'sub_section_description')->groupBy('module_id', 'permission_group', 'sub_section', 'sub_section_description')->get() as $sub_section)
                 @if (!$secondGroup)
                     <tr>
                 @endif
                 @php
                     $tracksubSectionCount += 1;
                     $subSectionId =
                         'module_id__' .
                         $module->id .
                         '__pmGroup__' .
                         $pmg->permission_group .
                         '__sub_section__' .
                         $sub_section->sub_section;
                 @endphp
                 <td>
                     <!-- Sub Sections -->

                     <div class="checkbox checkbox-inline checkbox-primary">
                         <input type="checkbox" @checked(in_array($subSectionId, $role_permissions))
                             class="menucls__{{ $menu_id }} pmgcls__{{ $permission_group_id }} sub_section_cls"
                             id="subSectionCls__{{ $subSectionId }}" value="{{ $subSectionId }}"
                             data-formenu="{{ $subSectionId }}" name="sub_section[]">
                         <label for="inlineCheckbox{{ $subSectionId }}" style="vertical-align: middle;">
                             {{ $sub_section->sub_section_description }}
                         </label>
                     </div>


                 </td>

                 <td>

                     <!-- Permissions -->
                     @php
                         $actionTypes = $module
                             ->permissionGroups()
                             ->select('actiontype')
                             ->where('permission_group', $pmg->permission_group)
                             ->where('module_id', $module->id)
                             ->where('sub_section', $sub_section->sub_section)
                             ->distinct('actiontype')
                             ->groupBy('actiontype')
                             ->pluck('actiontype')
                             ->toArray();
                     @endphp
                     @foreach ($actionTypes as $actiontype)
                         @php
                             $actionTypePermissions = $module
                                 ->permissionGroups()
                                 ->select('permission')
                                 ->where('permission_group', $pmg->permission_group)
                                 ->where('module_id', $module->id)
                                 ->where('sub_section', $sub_section->sub_section)
                                 ->where('actiontype', $actiontype)
                                 ->distinct('permission')
                                 ->groupBy('permission')
                                 ->orderBy('permission', 'asc')
                                 ->pluck('permission')
                                 ->toArray();
                             $actionTypeId = $subSectionId . '__' . $actiontype;
                            // dd($actionTypePermissions);
                         @endphp


                         <div class="checkbox checkbox-inline checkbox-primary {{ $actionTypeColors[$actiontype] }}">
                             <input type="checkbox" @checked(empty(array_diff($actionTypePermissions, $role_permissions)))
                                 class="menucls__{{ $menu_id }} pmgcls__{{ $permission_group_id }} subSectionCls__{{ $subSectionId }} action_type_cls"
                                 id="actionCls__{{ $actionTypeId }}" {{-- value="{{ $actiontype }}" data-formenu="{{ $module->menu_name }}"
                                   name="{{$module->id}}__{{$pmg->permission_group}}__{{$sub_section->sub_section}}__{{ $actiontype }}" --}}>
                             <label for="inlineCheckbox{{ $actiontype }}" style="vertical-align: middle;">
                                 {{ $actiontype }}
                             </label>
                             @foreach ($actionTypePermissions as $actionTypePm)
                                 <input type="checkbox" @checked(in_array($actionTypePm, $role_permissions)) hidden 
                                 class="menucls__{{ $menu_id }} pmgcls__{{ $permission_group_id }} subSectionCls__{{ $subSectionId }} actionCls__{{ $actionTypeId }}"
                                     id="inlineCheckbox{{ $actionTypeId }}__{{ $actionTypePm }}"
                                     value="{{ $actionTypePm }}" name="permission[]">
                             @endforeach
                         </div>
                     @endforeach

                 </td>

                 @if (!$secondGroup)
                     </tr>
                 @endif
                 @php $secondGroup = false; @endphp
             @endforeach
             @if (!$firstGroup)
                 </tr>
             @endif
             @php $firstGroup = false; @endphp
             @endforeach
             @if ($module->permissionGroups->isEmpty())
                 <td colspan="3">No Permission Groups</td>
             @endif
             </tr>
             @php
                 //dd($subSectionCount,$tracksubSectionCount,$tracksubSectionCount2);
             @endphp
             @endforeach
         </tbody>
     </table>
 </div>
