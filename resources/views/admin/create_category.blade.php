@extends('layout.private')
@section('title', 'GoToConsult - Create Category')
@section('content')
<?php $lang = app()->getLocale();?>
<div class="wrapper member-sidebar">
    @include('elements.admin_sidebar')
    <div class="content-wrapper adminprof">
        <div class="content_holesecion">
		    <div class="single-page d-flex flex-column">
                <div class="single-page-heading single-page d-flex flex-column">
                    <a href="{{ $lang == 'en' ? url('/categories') : url('/no/kategorier') }}"><img src="{{ asset('images/back-icon.png')}}" alt="icon"/></a>
                </div>
                <form method="post" id="upload_form" enctype="multipart/form-data">
                    <input type="hidden" id="hidden_id" name="hidden_id">
                    <input type="hidden" id="checkbox_value" name="checkbox_value" value=1>
			        <div class="profile-uploader d-flex flex-column">
				        <h2>@lang('admin.category_icon')</h2>
				        <div class="profile-sec single-cate d-flex flex-column">
					        <div class="imageupload">
						        <div class="file-tab">
                                    <label class="btn btn-default btn-file">
                                        <span>@lang('admin.browse_photo')</span>
                                        <input type="file" id="select_file" class="select_file" name="select_file">
						            </label>
        						</div>
					        </div>
					        <a class="remove-btn btn" id="remove_photo">@lang('admin.remove_image')</a>
					        <label class="switch">
                                <input type="checkbox" id="image_access" checked name="image_access" value="1">
                                <span class="slider"></span>
                                <span class="uncheck"></span>
                            </label>					
					        <input type="submit" name="upload" id="upload" class="sp-f cs btn save-btn" value="@lang('admin.upload')">
                            <div class="alert" id="message" style="display: none"></div>
                        </div>
			        </div>
                </form>
                <div class="page-setting single-cate d-flex flex-column">
                    <h2>@lang('admin.category_settings')</h2>
                    <div class="page-seting-content d-flex flex-column">
                        <label>@lang('admin.category_name')</label>
                        <input type="text" id="category_name" class="category_name">
                        <div class="alert" id="category_name_error"></div>
                        <label>@lang('admin.category_url')</label>
                        <div class="link-input d-flex">
                            <a href="#">https://gotoconsult.com/category/</a>
                            <input type="text" id="category_url" class="category_url">
                            <div class="alert" id="category_url_error"></div>
                        </div>
                        <label>@lang('admin.category_description')</label>
                        <textarea id="category_description" class="category_description"> </textarea>
                        <div class="alert" id="category_description_error"></div>
                        <button class="sp-f cs save-btn btn" id="profile_save">@lang('admin.save')</button>
                    </div>
                </div>
                <div class="page-setting meta-info d-flex flex-column">
                    <h2>@lang('admin.meta_information')</h2>
                    <div class="page-seting-content d-flex flex-column">
                        <label>@lang('admin.meta_title')</label>
                        <input type="text" id="meta_title" class="meta_title">
                        <div class="alert" id="meta_title_error"></div>
                        <label>@lang('admin.meta_description')</label>
                        <textarea rows="4" id="meta_description" class="meta_description" ></textarea>
                        <div class="alert" id="meta_description_error"></div>
                        <button class="sp-f cs save-btn btn" id="meta_save">@lang('admin.save')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/bootstrap-imageupload.js')}}"></script>
<script>
    $(document).ready(function(){
        $("#remove_photo").click(function(){
            $('.imageupload').imageupload();

            $('#imageupload-disable').on('click', function() {
                $('.imageupload').imageupload('disable');
                $(this).blur();
            });
            $('#imageupload-enable').on('click', function() {
                $('.imageupload').imageupload('enable');
                $(this).blur();
            });
            $('#imageupload-reset').on('click', function() {
                $('.imageupload').imageupload('reset');
                $(this).blur();
            });
        });

        $("#image_access").click(function(){
            if ($('#image_access').is(":checked")) {
                $("#checkbox_value").val(1);
            } else {
                $("#checkbox_value").val(0);     
            }
        });

        $('#upload_form').on('submit', function(event){
            event.preventDefault();
            if ($("#hidden_id").val() != '') {
                $.ajax({
                    url: '/create_category',
                    headers:  {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',      
                    data:new FormData(this),
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) { 
                        $('#message').css('display', 'block');
                        $('#message').html(data.message);
                        $('#message').addClass(data.class_name);
                        $('#uploaded_image').html(data.uploaded_image);
                        $('#message').hide(3000);
                    }
                });
            } else {
                alert("Please complete profile setting first.");
            }
        });

        $("#profile_save").click(function(){
            var category = {
                category_name: $("#category_name").val(),
                category_url: $("#category_url").val(),
                category_description: $("#category_description").val(),
                type: "profile"
            };
            $.ajax({
                url: '/create_category',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                data: category,
                dataType: 'JSON',
                success: function (data) { 
                    var status=JSON.stringify(data['status']);
                    if(status=='false') {
                        $.each(data.errors,function(index,value){
                            $("#" + index + "_error").show();
                            $("#" + index + "_error").text(value[0]);
                        });
                    } else {
                        var id=JSON.stringify(data['id']);
                        if(id!='') {
                            $("#hidden_id").val(id);
                            $("#category_name_error").hide();
                            $("#category_url_error").hide();
                            $("#category_description_error").hide();
                            alert("Category updated successfully");
                        } 
                    }
                }
            });
        });

        $("#meta_save").click(function(){
            var meta_info = {
                meta_title: $("#meta_title").val(),
                meta_description: $("#meta_description").val(),
                hidden_id: $("#hidden_id").val(),
                type: 'meta'
            };
            if ($("#hidden_id").val() != '') {
                $.ajax({
                    url: '/create_category',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    type: 'POST',
                    data: meta_info,
                    dataType: 'JSON',
                    success: function (data) { 
                        var status=JSON.stringify(data['status']);
                        if( !status) {
                            $.each(data.errors,function(index,value){
                                $("#" + index + "_error").show();
                                $("#" + index + "_error").text(value[0]);
                            });
                        } else {
                            var id=JSON.stringify(data['id']);
                            if(id!='') {
                                $("#meta_title_error").hide();
                                $("#meta_description_error").hide();
                                alert("Meta data updated successfully");
                            } 
                        }
                    }
                });
            } else {
                alert("please complete profile setting first.");
            }
        });
    });
</script>
@endsection