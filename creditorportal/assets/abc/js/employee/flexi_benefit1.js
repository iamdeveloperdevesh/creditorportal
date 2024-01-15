$(document).ready(function(){
     $.ajax({
          url:'/check_enrollment',
          type: "POST",
          async: true,
              dataType: "json",
              success: function (response) {
                if (response.length == 0) 
                {
                    $(".arrow-proceed").css('pointer-events','none');
                     $("#health_checkup").css('pointer-events','none');
                     $("#dependent_care").css('pointer-events','none');
                     $("#dental_care").css('pointer-events','none');
                     $("#yoga").css('pointer-events','none');
                     $("#elder_care").css('pointer-events','none');
                     $("#gym_care").css('pointer-events','none');
                     $("#cmp_care").css('pointer-events','none');
                }
              }
        });
      $.ajax({
          url:'/flexi_benefit/get_emp_data_flexi',
          type: "POST",
          async: true,
              dataType: "json",
              success: function (response) {
               $.each(response, function( index, value ) {
                    if (value.policy_sub_type_name == 'GMC') 
                    {
                      $("#gmc_id").removeAttr('style','display:none');
                      $("#si").text(value.policy_mem_sum_insured);
                    }

                    if (value.policy_sub_type_name == 'GTLI') 
                    {
                      $("#gtli_id").removeAttr('style','display:none');
                      $("#si").text(value.policy_mem_sum_insured);
                    }

                    if (value.policy_sub_type_name == 'GPA') 
                    {
                      $("#gpa_id").removeAttr('style','display:none');
                      $("#si").text(value.policy_mem_sum_insured);
                    }
               });
              }
        });
            $.ajax({
          url:'/flexi_benifit/get_ghi_policy_member',
          type: "POST",
          async: true,
              dataType: "json",
              success: function (response) {
               $.each(response, function( index, value ) {
                $("#gmc_id").append('<div class="col-md-6"><span class="color-bul">'+value.name+' '+value.last_name+'</span></div><div class="col-md-6"><span class="color-bul"></span></div>');
               //      if (value.policy_sub_type_name == 'GMC') 
               //      {
               //        $("#gmc_id").removeAttr('style','display:none');
               //        $("#si").text(value.policy_mem_sum_insured);
               //      }

               //      if (value.policy_sub_type_name == 'GTLI') 
               //      {
               //        $("#gtli_id").removeAttr('style','display:none');
               //        $("#si").text(value.policy_mem_sum_insured);
               //      }

               //      if (value.policy_sub_type_name == 'GPA') 
               //      {
               //        $("#gpa_id").removeAttr('style','display:none');
               //        $("#si").text(value.policy_mem_sum_insured);
               //      }
               });
              }
        });
          $.ajax({
                url: "/flexi_benifit/get_utilised_data",
                type: "POST",
                async: true,
                dataType: "json",
                success: function (response) {
                    if(response.flex_data == null)
                    {
                        $(".flex_utilised").html("<i class='fa fa-inr'></i>0");
                        $(".flex_utilised").attr('data-value',0);
                    }else
                    {
                       $(".flex_utilised").html("<i class='fa fa-inr'></i>"+response.flex_data);
                       $(".flex_utilised").attr('data-value',response.flex_data);
                    }
                    if(response.salary_data == null)
                    {
                        $(".salary_deduction").html("<i class='fa fa-inr'></i>0");
                         $(".salary_deduction").attr('data-value',0);
                    }else
                    {
                       $(".salary_deduction").html("<i class='fa fa-inr'></i>"+response.salary_data);
                       $(".salary_deduction").attr('data-value',response.salary_data);
                    }
                    if(response.benifit_data !== null)
                    {
                        $.each(response.benifit_data, function( index, value ) {
                            $("#benifit_"+value.master_flexi_benefit_id).find(".estimate_box").html("<i class='fa fa-inr'></i> "+value.final_amount);
                            $("#benifit_"+value.master_flexi_benefit_id).find(".estimate_box").attr("data-value",value.final_amount);
                            $("#benifit_"+value.master_flexi_benefit_id).find(".estimate_box").attr("data-type",value.deduction_type);
                            $("#benifit_"+value.master_flexi_benefit_id).find(".tick-1").removeClass('hidden');
                            $("#benifit_"+value.master_flexi_benefit_id).find(".payment_type[value='"+value.deduction_type+"']").click();
                            $("#benifit_"+value.master_flexi_benefit_id).find(".sum_insured_check[value='"+value.sum_insured+"']").click();
                            if(value.master_flexi_benefit_id == '1')
                            {
                              $("#benifit_"+value.master_flexi_benefit_id).find("input[value='"+(value.final_amount/12)+"']").click();
                            }
                          });
                    }
            }
        });
         $.ajax({
                url: "/flexi_benefit/get_all_active_flexi_from_master",
                type: "POST",
                async: true,
                dataType: "json",
                success: function (response) {
                 $.each(response,function(i,v){
                    $("#benifit_"+v.master_flexi_benefit_id+"").removeAttr('style','display:none');
                 })
                }
       });


         $("#home1-tab").click(function() {
            $(".payment_type:checked").map(function(){
                    $(this).closest('.slide-flex').find('.estimate_box').attr('data-value',"0");
                    $("#sudexo_estimate").attr('data-value', "0");
                    
                });
         });
        
        $("#submitSudexo").click(function(){
            var selected_val = $("input[name='sudexo_radio']:checked").val();
            var anual = selected_val*12;
             var current_val = $("#sudexo_estimate").attr('data-value');
            var flex_utilised =  parseInt($(".flex_utilised").attr('data-value')) + parseInt(anual) - parseInt((current_val == '') ? 0 : current_val);
            if(flex_utilised > $(".flex_alloted").attr('data-value'))
            {
              $("#getCodeModal").modal("toggle");
              $("#getCode").html('Flex balance is not enough');
            }
            $(".flex_utilised").html("<i class='fa fa-inr'></i> "+flex_utilised);
            $(".flex_utilised").attr("data-value",flex_utilised);
            $("#sudexo_estimate").html("<i class='fa fa-inr'></i> "+anual);
            $("#sudexo_estimate").attr("data-value",anual);
        });
        $('input[type=radio][name=ghi_si]').change(function () {
           var ghi_data = $("input[name='ghi_si']:checked").val();
           
           if(ghi_data == 3){
            $("#ghi_total_premium").html('10000');
          }

          if (ghi_data == 5) {
            $("#ghi_total_premium").html('14000');
          }

          if (ghi_data == 10) {
            $("#ghi_total_premium").html('18000');
          }
          if (ghi_data == 15)
          {
            $("#ghi_total_premium").html('20000');
          }
            // $("#ghi_total_premium").html($("input[name='ghi_si']:checked").val()*600);
        });
        $("input[name='ghi_si']").click(function(){
          var ghi_data = $("input[name='ghi_si']:checked").val();
           if(ghi_data == 3){
            $("#ghi_estimate").html('10000');
          }

          if (ghi_data == 5) {
            $("#ghi_estimate").html('14000');
          }

          if (ghi_data == 10) {
            $("#ghi_estimate").html('18000');
          }
          if (ghi_data == 15)
          {
            $("#ghi_estimate").html('20000');
          }
          // $("#ghi_total_premium").html($(this).val()*600);  
        });
        
        $("#submitGHI").click(function(){
          $('input[type=radio][name=ghi_si]').change();
            var deduction_type = $("input[name='ghi_type']:checked").val();
            var current_val = $("#ghi_estimate").attr('data-value');
            console.log(current_val);
            if(deduction_type == 'F')
            {
                if($("#ghi_estimate").attr("data-type") == 'S')
                {
                    $(".salary_deduction").html("<i class='fa fa-inr'></i> "+(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#ghi_estimate").text())));
                    $(".salary_deduction").attr("data-value",(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#ghi_estimate").text())));
                    $("#ghi_estimate").attr('data-value','0');
                }               
                var flex_utilised =  parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#ghi_total_premium").text()) - parseInt((($("#ghi_estimate").attr('data-value') == '') ? 0 : $("#ghi_estimate").attr('data-value')));
                if(flex_utilised > $(".flex_alloted").attr('data-value'))
               {
                  $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Flex balance is not enough');
               }
               $(".flex_utilised").html("<i class='fa fa-inr'></i> "+flex_utilised);
               $(".flex_utilised").attr("data-value",flex_utilised);
               $("#ghi_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#ghi_total_premium").text()));
               $("#ghi_estimate").attr("data-value",parseInt($("#ghi_total_premium").text()));
               $("#ghi_estimate").attr("data-type",'F');
            }else
            {
                if($("#ghi_estimate").attr("data-type") == 'F')
                { 
                    $(".flex_utilised").html("<i class='fa fa-inr'></i> "+(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#ghi_estimate").text())));
                    $(".flex_utilised").attr("data-value",(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#ghi_estimate").attr('data-value'))));
                    $("#ghi_estimate").attr('data-value','0');
                }                
                var salary_deduction =  parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#ghi_total_premium").text()) - parseInt(($("#ghi_estimate").attr('data-value') == '') ? 0 : $("#ghi_estimate").attr('data-value'));
                if(salary_deduction > $("#total_salary").val())
                {
                   $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Salary is not enough');
                }
                $(".salary_deduction").html("<i class='fa fa-inr'></i> "+salary_deduction);
                $(".salary_deduction").attr("data-value",salary_deduction);
                $("#ghi_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#ghi_total_premium").text()));
                $("#ghi_estimate").attr("data-value",parseInt($("#ghi_total_premium").text()));
                $("#ghi_estimate").attr("data-type",'S');
            }
        });
       // parental
        $("#add_parential").click(function(){
            var deduction_type = $("input[name='pc_type']:checked").val();
            var current_val = $("#parental_estimate").attr('data-value');
            if(deduction_type == 'F')
            {
                if($("#parental_estimate").attr("data-type") == 'S')
                {
                    $(".salary_deduction").html("<i class='fa fa-inr'></i> "+(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#parental_estimate").text())));
                    $(".salary_deduction").attr("data-value",(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#parental_estimate").text())));
                    $("#parental_estimate").attr('data-value','0');
                }               
                var flex_utilised =  parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#parental_total_premium").val()) - parseInt((($("#parental_estimate").attr('data-value') == '') ? 0 : $("#parental_estimate").attr('data-value')));
                if(flex_utilised > $(".flex_alloted").attr('data-value'))
               {
                   alert('Flex balance is not enough');
                   return false;
               }
               $(".flex_utilised").html("<i class='fa fa-inr'></i> "+flex_utilised);
               $(".flex_utilised").attr("data-value",flex_utilised);
               $("#parental_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#parental_total_premium").val()));
               $("#parental_estimate").attr("data-value",parseInt($("#parental_total_premium").val()));
               $("#parental_estimate").attr("data-type",'F');
            }else
            {
                if($("#parental_estimate").attr("data-type") == 'F')
                {
                    $(".flex_utilised").html("<i class='fa fa-inr'></i> "+(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#parental_estimate").text())));
                    $(".flex_utilised").attr("data-value",(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#parental_estimate").attr('data-value'))));
                    $("#parental_estimate").attr('data-value','0');
                }                
                var salary_deduction =  parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#parental_total_premium").val()) - parseInt(($("#parental_estimate").attr('data-value') == '') ? 0 : $("#parental_estimate").attr('data-value'));
                if(salary_deduction > $("#total_salary").val())
                {
                    alert('Salary is not enough');
                    return false;
                }
                $(".salary_deduction").html("<i class='fa fa-inr'></i> "+salary_deduction);
                $(".salary_deduction").attr("data-value",salary_deduction);
                $("#parental_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#parental_total_premium").val()));
                $("#parental_estimate").attr("data-value",parseInt($("#parental_total_premium").val()));
                $("#parental_estimate").attr("data-type",'S');
            }
          });
        // gpa
              $('input[type=radio][name=gpa_si]').change(function () {
            $("#gpa_total_premium").html($("input[name='gpa_si']:checked").val()*40);
        });
        $("input[name='gpa_si']").click(function(){
          // $("#gpa_total_premium").html($(this).val()*40);
          $("#gpa_estimate").html($(this).val()*40);   
        });
        
        $("#submitGPA").click(function(){
            var deduction_type = $("input[name='gpa_type']:checked").val();
            $('input[type=radio][name=gpa_si]').change();
            var current_val = $("#gpa_estimate").attr('data-value');
            if(deduction_type == 'F')
            {
                if($("#gpa_estimate").attr("data-type") == 'S')
                {
                    $(".salary_deduction").html("<i class='fa fa-inr'></i> "+(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#gpa_estimate").attr('data-value'))));
                    $(".salary_deduction").attr("data-value",(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#gpa_estimate").attr('data-value'))));
                    $("#gpa_estimate").attr('data-value','0');
                }               
                var flex_utilised =  parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#gpa_total_premium").text()) - parseInt(($("#gpa_estimate").attr('data-value') == '') ? 0 : $("#gpa_estimate").attr('data-value'));
                if(flex_utilised > $(".flex_alloted").attr('data-value'))
               {
                   $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Flex balance is not enough');
               }
               $(".flex_utilised").html("<i class='fa fa-inr'></i> "+flex_utilised);
               $(".flex_utilised").attr("data-value",flex_utilised);
               $("#gpa_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#gpa_total_premium").text()));
               $("#gpa_estimate").attr("data-value",parseInt($("#gpa_total_premium").text()));
               $("#gpa_estimate").attr("data-type",'F');
            }else
            {
                if($("#gpa_estimate").attr("data-type") == 'F')
                {
                    $(".flex_utilised").html("<i class='fa fa-inr'></i> "+(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#gpa_estimate").attr('data-value'))));
                    $(".flex_utilised").attr("data-value",(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#gpa_estimate").attr('data-value'))));
                    $("#gpa_estimate").attr('data-value','0');
                }                
                var salary_deduction =  parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#gpa_total_premium").text()) - parseInt(($("#gpa_estimate").attr('data-value') == '') ? 0 : $("#gpa_estimate").attr('data-value'));
                if(salary_deduction > $("#total_salary").val())
                {
                  $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Salary is not enough');
                }
                $(".salary_deduction").html("<i class='fa fa-inr'></i> "+salary_deduction);
                $(".salary_deduction").attr("data-value",salary_deduction);
                $("#gpa_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#gpa_total_premium").text()));
                $("#gpa_estimate").attr("data-value",parseInt($("#gpa_total_premium").text()));
                $("#gpa_estimate").attr("data-type",'S');
            }
        });
            //glta
             $('input[type=radio][name=glta_si]').change(function () {
            $("#glta_total_premium").html($("input[name='glta_si']:checked").val()*40);
        });
        $("input[name='glta_si']").click(function(){
          // $("#glta_total_premium").html($(this).val()*40);  
         // $("#glta_estimate").html($(this).val()*40);
        });
        
        $("#submitGLTA").click(function(){
          getPremiumCalc();

            var deduction_type = $("input[name='glta_type']:checked").val();
            var current_val = $("#glta_estimate").attr('data-value');
            if(deduction_type == 'F')
            {
               if($("#glta_estimate").attr("data-type") == 'S')
                {
                    $(".salary_deduction").html("<i class='fa fa-inr'></i> "+(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#glta_estimate").attr('data-value'))));
                    $(".salary_deduction").attr("data-value",(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#glta_estimate").attr('data-value'))));
                    $("#glta_estimate").attr('data-value','0');
                }               
                
                var flex_utilised =  parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#glta_total_premium").text()) - parseInt(($("#glta_estimate").attr('data-value') == '') ? 0 : $("#glta_estimate").attr('data-value'));
                if(flex_utilised > $(".flex_alloted").attr('data-value'))
               {
                 $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Flex balance is not enough');
               }
               $(".flex_utilised").html("<i class='fa fa-inr'></i> "+flex_utilised);
               $(".flex_utilised").attr("data-value",flex_utilised);
               $("#glta_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#glta_total_premium").text()));
               $("#glta_estimate").attr("data-value",parseInt($("#glta_total_premium").text()));
               $("#glta_estimate").attr("data-type",'F');
            }else
            {
              if($("#glta_estimate").attr("data-type") == 'F')
                {
                    $(".flex_utilised").html("<i class='fa fa-inr'></i> "+(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#glta_estimate").attr('data-value'))));
                    $(".flex_utilised").attr("data-value",(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#glta_estimate").attr('data-value'))));
                    $("#glta_estimate").attr('data-value','0');
                }                
                var salary_deduction =  parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#glta_total_premium").text()) - parseInt(($("#glta_estimate").attr('data-value') == '') ? 0 : $("#glta_estimate").attr('data-value'));
                if(salary_deduction > $("#total_salary").val())
                {
                  $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Salary is not enough');se;
                }
                $(".salary_deduction").html("<i class='fa fa-inr'></i> "+salary_deduction);
                $(".salary_deduction").attr("data-value",salary_deduction);
                $("#glta_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#glta_total_premium").text()));
                $("#glta_estimate").attr("data-value",parseInt($("#glta_total_premium").text()));
                $("#glta_estimate").attr("data-type",'S');
            }          
            
        });
        //health checkup
              $("#hc_modal_btn").click(function (){
            $("#hc_total_premium").val($("#hc_estimate").attr("data-value"));
        });
        
        $("#submitHC").click(function(){
            var deduction_type = $("input[name='hc_type']:checked").val();
            var current_val = $("#hc_estimate").attr('data-value');
            if(deduction_type == 'F')
            {
                if($("#hc_estimate").attr("data-type") == 'S')
                {
                    $(".salary_deduction").html("<i class='fa fa-inr'></i> "+(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#hc_estimate").attr('data-value'))));
                    $(".salary_deduction").attr("data-value",(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#hc_estimate").attr('data-value'))));
                    $("#hc_estimate").attr('data-value','0');
                }               
                var flex_utilised =  parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#hc_total_premium").val()) - parseInt(($("#hc_estimate").attr('data-value') == '') ? 0 : $("#hc_estimate").attr('data-value'));
                if(flex_utilised > $(".flex_alloted").attr('data-value'))
               {
                  $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Flex balance is not enough');
               }
               $(".flex_utilised").html("<i class='fa fa-inr'></i> "+flex_utilised);
               $(".flex_utilised").attr("data-value",flex_utilised);
               $("#hc_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#hc_total_premium").val()));
               $("#hc_estimate").attr("data-value",parseInt($("#hc_total_premium").val()));
               $("#hc_estimate").attr("data-type",'F');
            }else
            {
                if($("#hc_estimate").attr("data-type") == 'F')
                {
                    $(".flex_utilised").html("<i class='fa fa-inr'></i> "+(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#hc_estimate").attr('data-value'))));
                    $(".flex_utilised").attr("data-value",(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#hc_estimate").attr('data-value'))));
                    $("#hc_estimate").attr('data-value','0');
                }                
                var salary_deduction =  parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#hc_total_premium").val()) - parseInt(($("#hc_estimate").attr('data-value') == '') ? 0 : $("#hc_estimate").attr('data-value'));
                if(salary_deduction > $("#total_salary").val())
                {
                  $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Salary is not enough');
                }

                $(".salary_deduction").html("<i class='fa fa-inr'></i> "+salary_deduction);
                $(".salary_deduction").attr("data-value",salary_deduction);
                $("#hc_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#hc_total_premium").val()));
                $("#hc_estimate").attr("data-value",parseInt($("#hc_total_premium").val()));
                $("#hc_estimate").attr("data-type",'S');
            }  
        }); 
        //dependant care
        
           $("#dc_modal_btn").click(function (){
            $("#dc_total_premium").val($("#dc_estimate").attr("data-value"));
        });
        
        $("#submitDC").click(function(){
            var deduction_type = $("input[name='dc_type']:checked").val();
            var current_val = $("#dc_estimate").attr('data-value');
            if(deduction_type == 'F')
            {
                if($("#dc_estimate").attr("data-type") == 'S')
                {
                    $(".salary_deduction").html("<i class='fa fa-inr'></i> "+(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#dc_estimate").attr('data-value'))));
                    $(".salary_deduction").attr("data-value",(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#dc_estimate").attr('data-value'))));
                    $("#dc_estimate").attr('data-value','0');
                }               
                var flex_utilised =  parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#dc_total_premium").val()) - parseInt(($("#dc_estimate").attr('data-value') == '') ? 0 : $("#dc_estimate").attr('data-value'));
                if(flex_utilised > $(".flex_alloted").attr('data-value'))
               {

                  $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Flex balance is not enough');
               }
               $(".flex_utilised").html("<i class='fa fa-inr'></i> "+flex_utilised);
               $(".flex_utilised").attr("data-value",flex_utilised);
               $("#dc_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#dc_total_premium").val()));
               $("#dc_estimate").attr("data-value",parseInt($("#dc_total_premium").val()));
               $("#dc_estimate").attr("data-type",'F');
            }else
            {
                if($("#dc_estimate").attr("data-type") == 'F')
                {
                    $(".flex_utilised").html("<i class='fa fa-inr'></i> "+(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#dc_estimate").attr('data-value'))));
                    $(".flex_utilised").attr("data-value",(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#dc_estimate").attr('data-value'))));
                    $("#dc_estimate").attr('data-value','0');
                }                
                var salary_deduction =  parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#dc_total_premium").val()) - parseInt(($("#dc_estimate").attr('data-value') == '') ? 0 : $("#dc_estimate").attr('data-value'));
                if(salary_deduction > $("#total_salary").val())
                {
                   $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Salary is not enough');
                }
                $(".salary_deduction").html("<i class='fa fa-inr'></i> "+salary_deduction);
                $(".salary_deduction").attr("data-value",salary_deduction);
                $("#dc_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#dc_total_premium").val()));
                $("#dc_estimate").attr("data-value",parseInt($("#dc_total_premium").val()));
                $("#dc_estimate").attr("data-type",'S');
            }     
        });          
        //dental care
             $("#dnc_modal_btn").click(function (){
            $("#dnc_total_premium").val($("#dnc_estimate").attr("data-value"));
        });
        $("#submitDNC").click(function(){
            var deduction_type = $("input[name='dnc_type']:checked").val();
            var current_val = $("#dnc_estimate").attr('data-value');
            if(deduction_type == 'F')
            {
                if($("#dnc_estimate").attr("data-type") == 'S')
                {
                    $(".salary_deduction").html("<i class='fa fa-inr'></i> "+(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#dnc_estimate").attr('data-value'))));
                    $(".salary_deduction").attr("data-value",(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#dnc_estimate").attr('data-value'))));
                    $("#dnc_estimate").attr('data-value','0');
                }               
                var flex_utilised =  parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#dnc_total_premium").val()) - parseInt(($("#dnc_estimate").attr('data-value') == '') ? 0 : $("#dnc_estimate").attr('data-value'));
                if(flex_utilised > $(".flex_alloted").attr('data-value'))
               {
                $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Flex balance is not enough');
               }
               $(".flex_utilised").html("<i class='fa fa-inr'></i> "+flex_utilised);
               $(".flex_utilised").attr("data-value",flex_utilised);
               $("#dnc_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#dnc_total_premium").val()));
               $("#dnc_estimate").attr("data-value",parseInt($("#dnc_total_premium").val()));
               $("#dnc_estimate").attr("data-type",'F');
            }else
            {
                if($("#dnc_estimate").attr("data-type") == 'F')
                {
                    $(".flex_utilised").html("<i class='fa fa-inr'></i> "+(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#dnc_estimate").attr('data-value'))));
                    $(".flex_utilised").attr("data-value",(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#dnc_estimate").attr('data-value'))));
                    $("#dnc_estimate").attr('data-value','0');
                }                
                var salary_deduction =  parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#dnc_total_premium").val()) - parseInt(($("#dnc_estimate").attr('data-value') == '') ? 0 : $("#dnc_estimate").attr('data-value'));
                if(salary_deduction > $("#total_salary").val())
                {
                   $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Salary is not enough');
                }
                $(".salary_deduction").html("<i class='fa fa-inr'></i> "+salary_deduction);
                $(".salary_deduction").attr("data-value",salary_deduction);
                $("#dnc_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#dnc_total_premium").val()));
                $("#dnc_estimate").attr("data-value",parseInt($("#dnc_total_premium").val()));
                $("#dnc_estimate").attr("data-type",'S');
            }   
        });     
        //teleconsultation
            $("#tc_modal_btn").click(function (){
            $("#tc_total_premium").val($("#tc_estimate").attr("data-value"));
        });
        
        $("#submitTC").click(function(){
            var deduction_type = $("input[name='tc_type']:checked").val();
            var current_val = $("#tc_estimate").attr('data-value');
            if(deduction_type == 'F')
            {
                if($("#tc_estimate").attr("data-type") == 'S')
                {
                    $(".salary_deduction").html("<i class='fa fa-inr'></i> "+(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#tc_estimate").attr('data-value'))));
                    $(".salary_deduction").attr("data-value",(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#tc_estimate").attr('data-value'))));
                    $("#tc_estimate").attr('data-value','0');
                }               
                var flex_utilised =  parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#tc_total_premium").val()) - parseInt(($("#tc_estimate").attr('data-value') == '') ? 0 : $("#tc_estimate").attr('data-value'));
                if(flex_utilised > $(".flex_alloted").attr('data-value'))
               {
                $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Flex balance is not enough');
               }
               $(".flex_utilised").html("<i class='fa fa-inr'></i> "+flex_utilised);
               $(".flex_utilised").attr("data-value",flex_utilised);
               $("#tc_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#tc_total_premium").val()));
               $("#tc_estimate").attr("data-value",parseInt($("#tc_total_premium").val()));
               $("#tc_estimate").attr("data-type",'F');
            }else
            {
                if($("#tc_estimate").attr("data-type") == 'F')
                {
                    $(".flex_utilised").html("<i class='fa fa-inr'></i> "+(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#tc_estimate").attr('data-value'))));
                    $(".flex_utilised").attr("data-value",(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#tc_estimate").attr('data-value'))));
                    $("#tc_estimate").attr('data-value','0');
                }                
                var salary_deduction =  parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#tc_total_premium").val()) - parseInt(($("#tc_estimate").attr('data-value') == '') ? 0 : $("#tc_estimate").attr('data-value'));
                if(salary_deduction > $("#total_salary").val())
                {
                  $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Salary is not enough');
                }
                $(".salary_deduction").html("<i class='fa fa-inr'></i> "+salary_deduction);
                $(".salary_deduction").attr("data-value",salary_deduction);
                $("#tc_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#tc_total_premium").val()));
                $("#tc_estimate").attr("data-value",parseInt($("#tc_total_premium").val()));
                $("#tc_estimate").attr("data-type",'S');
            } 
        }); 
        //yoga zumba
            $("#yz_modal_btn").click(function (){
            $("#yz_total_premium").val($("#yz_estimate").attr("data-value"));
        });
        
        $("#submitYZ").click(function(){
            var deduction_type = $("input[name='yz_type']:checked").val();
            var current_val = $("#yz_estimate").attr('data-value');
            if(deduction_type == 'F')
            {
                if($("#yz_estimate").attr("data-type") == 'S')
                {
                    $(".salary_deduction").html("<i class='fa fa-inr'></i> "+(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#yz_estimate").attr('data-value'))));
                    $(".salary_deduction").attr("data-value",(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#yz_estimate").attr('data-value'))));
                    $("#yz_estimate").attr('data-value','0');
                }               
                var flex_utilised =  parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#yz_total_premium").val()) - parseInt(($("#yz_estimate").attr('data-value') == '') ? 0 : $("#yz_estimate").attr('data-value'));
                if(flex_utilised > $(".flex_alloted").attr('data-value'))
               {
                  $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Flex balance is not enough');
               }
               $(".flex_utilised").html("<i class='fa fa-inr'></i> "+flex_utilised);
               $(".flex_utilised").attr("data-value",flex_utilised);
               $("#yz_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#yz_total_premium").val()));
               $("#yz_estimate").attr("data-value",parseInt($("#yz_total_premium").val()));
               $("#yz_estimate").attr("data-type",'F');
            }else
            {
                if($("#yz_estimate").attr("data-type") == 'F')
                {
                    $(".flex_utilised").html("<i class='fa fa-inr'></i> "+(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#yz_estimate").attr('data-value'))));
                    $(".flex_utilised").attr("data-value",(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#yz_estimate").attr('data-value'))));
                    $("#yz_estimate").attr('data-value','0');
                }                
                var salary_deduction =  parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#yz_total_premium").val()) - parseInt(($("#yz_estimate").attr('data-value') == '') ? 0 : $("#yz_estimate").attr('data-value'));
                if(salary_deduction > $("#total_salary").val())
                {
                  $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Salary is not enough');
                }

                $(".salary_deduction").html("<i class='fa fa-inr'></i> "+salary_deduction);
                $(".salary_deduction").attr("data-value",salary_deduction);
                $("#yz_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#yz_total_premium").val()));
                $("#yz_estimate").attr("data-value",parseInt($("#yz_total_premium").val()));
                $("#yz_estimate").attr("data-type",'S');
            } 
        });
        //elder care
            $("#ec_modal_btn").click(function (){
            $("#ec_total_premium").val($("#ec_estimate").attr("data-value"));
        });
        
        $("#submitEC").click(function(){
            var deduction_type = $("input[name='ec_type']:checked").val();
            var current_val = $("#ec_estimate").attr('data-value');
            if(deduction_type == 'F')
            {
                if($("#ec_estimate").attr("data-type") == 'S')
                {
                    $(".salary_deduction").html("<i class='fa fa-inr'></i> "+(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#ec_estimate").attr('data-value'))));
                    $(".salary_deduction").attr("data-value",(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#ec_estimate").attr('data-value'))));
                    $("#ec_estimate").attr('data-value','0');
                }               
                var flex_utilised =  parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#ec_total_premium").val()) - parseInt(($("#ec_estimate").attr('data-value') == '') ? 0 : $("#ec_estimate").attr('data-value'));
                if(flex_utilised > $(".flex_alloted").attr('data-value'))
               {
                 $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Flex balance is not enough');
               }
               $(".flex_utilised").html("<i class='fa fa-inr'></i> "+flex_utilised);
               $(".flex_utilised").attr("data-value",flex_utilised);
               $("#ec_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#ec_total_premium").val()));
               $("#ec_estimate").attr("data-value",parseInt($("#ec_total_premium").val()));
               $("#ec_estimate").attr("data-type",'F');
            }else
            {
                if($("#ec_estimate").attr("data-type") == 'F')
                {
                    $(".flex_utilised").html("<i class='fa fa-inr'></i> "+(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#ec_estimate").attr('data-value'))));
                    $(".flex_utilised").attr("data-value",(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#ec_estimate").attr('data-value'))));
                    $("#ec_estimate").attr('data-value','0');
                }                
                var salary_deduction =  parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#ec_total_premium").val()) - parseInt(($("#ec_estimate").attr('data-value') == '') ? 0 : $("#ec_estimate").attr('data-value'));
                if(salary_deduction > $("#total_salary").val())
                {
                  $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Salary is not enough');
                }
                $(".salary_deduction").html("<i class='fa fa-inr'></i> "+salary_deduction);
                $(".salary_deduction").attr("data-value",salary_deduction);
                $("#ec_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#ec_total_premium").val()));
                $("#ec_estimate").attr("data-value",parseInt($("#ec_total_premium").val()));
                $("#ec_estimate").attr("data-type",'S');
            } 
        });        
        //gym
             $("#gym_modal_btn").click(function (){
            $("#gym_total_premium").val($("#gym_estimate").attr("data-value"));
        });
        $("#submitGYM").click(function(){
            var deduction_type = $("input[name='gym_type']:checked").val();
            var current_val = $("#gym_estimate").attr('data-value');
            if(deduction_type == 'F')
            {
                if($("#gym_estimate").attr("data-type") == 'S')
                {
                    $(".salary_deduction").html("<i class='fa fa-inr'></i> "+(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#gym_estimate").attr('data-value'))));
                    $(".salary_deduction").attr("data-value",(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#gym_estimate").attr('data-value'))));
                    $("#gym_estimate").attr('data-value','0');
                }               
                var flex_utilised =  parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#gym_total_premium").val()) - parseInt(($("#gym_estimate").attr('data-value') == '') ? 0 : $("#gym_estimate").attr('data-value'));
                if(flex_utilised > $(".flex_alloted").attr('data-value'))
                {
                  $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Flex balance is not enough');
               }
               $(".flex_utilised").html("<i class='fa fa-inr'></i> "+flex_utilised);
               $(".flex_utilised").attr("data-value",flex_utilised);
               $("#gym_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#gym_total_premium").val()));
               $("#gym_estimate").attr("data-value",parseInt($("#gym_total_premium").val()));
               $("#gym_estimate").attr("data-type",'F');
            }else
            {
                if($("#gym_estimate").attr("data-type") == 'F')
                {
                    $(".flex_utilised").html("<i class='fa fa-inr'></i> "+(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#gym_estimate").attr('data-value'))));
                    $(".flex_utilised").attr("data-value",(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#gym_estimate").attr('data-value'))));
                    $("#gym_estimate").attr('data-value','0');
                }                
                var salary_deduction =  parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#gym_total_premium").val()) - parseInt(($("#gym_estimate").attr('data-value') == '') ? 0 : $("#gym_estimate").attr('data-value'));
                if(salary_deduction > $("#total_salary").val())
                {
                  $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Salary is not enough');
                }
                $(".salary_deduction").html("<i class='fa fa-inr'></i> "+salary_deduction);
                $(".salary_deduction").attr("data-value",salary_deduction);
                $("#gym_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#gym_total_premium").val()));
                $("#gym_estimate").attr("data-value",parseInt($("#gym_total_premium").val()));
                $("#gym_estimate").attr("data-type",'S');
            }  
        }); 
        //cmp
             $("#cmp_modal_btn").click(function (){
            $("#cmp_total_premium").val($("#cmp_estimate").attr("data-value"));
        });
        $("#submitcmp").click(function(){
            var deduction_type = $("input[name='cmp_type']:checked").val();
            var current_val = $("#cmp_estimate").attr('data-value');
            if(deduction_type == 'F')
            {
                if($("#cmp_estimate").attr("data-type") == 'S')
                {
                    $(".salary_deduction").html("<i class='fa fa-inr'></i> "+(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#cmp_estimate").attr('data-value'))));
                    $(".salary_deduction").attr("data-value",(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#cmp_total_premium").attr('data-value'))));
                    $("#cmp_estimate").attr('data-value','0');
                }               
                var flex_utilised =  parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#cmp_total_premium").val()) - parseInt(($("#cmp_estimate").attr('data-value') == '') ? 0 : $("#cmp_estimate").attr('data-value'));
              if(flex_utilised > $(".flex_alloted").attr('data-value'))
               {
                  $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Flex balance is not enough');
               }
               $(".flex_utilised").html("<i class='fa fa-inr'></i> "+flex_utilised);
               $(".flex_utilised").attr("data-value",flex_utilised);
               $("#cmp_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#cmp_total_premium").val()));
               $("#cmp_estimate").attr("data-value",parseInt($("#cmp_total_premium").val()));
               $("#cmp_estimate").attr("data-type",'F');
            }else
            {
              if($("#cmp_estimate").attr("data-type") == 'F')
                {
                    $(".flex_utilised").html("<i class='fa fa-inr'></i> "+(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#cmp_estimate").attr('data-value'))));
                    $(".flex_utilised").attr("data-value",(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#cmp_estimate").attr('data-value'))));
                    $("#cmp_estimate").attr('data-value','0');
                }                
                var salary_deduction =  parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#cmp_total_premium").val()) - parseInt(($("#cmp_estimate").attr('data-value') == '') ? 0 : $("#cmp_estimate").attr('data-value'));
                if(salary_deduction > $("#total_salary").val())
                {
                  $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Salary is not enough');
                }
                $(".salary_deduction").html("<i class='fa fa-inr'></i> "+salary_deduction);
                $(".salary_deduction").attr("data-value",salary_deduction);
                $("#cmp_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#cmp_total_premium").val()));
                $("#cmp_estimate").attr("data-value",parseInt($("#cmp_total_premium").val()));
                $("#cmp_estimate").attr("data-type",'S');
            }
        });

        //nutri
        $("#nutri_modal_btn").click(function (){
            $("#nutri_total_premium").val($("#nutri_estimate").attr("data-value"));
        });
        $("#submitnutri").click(function(){
            var deduction_type = $("input[name='nutri_type']:checked").val();
            var current_val = $("#nutri_estimate").attr('data-value');
            if(deduction_type == 'F')
            {
                if($("#nutri_estimate").attr("data-type") == 'S')
                {
                    $(".salary_deduction").html("<i class='fa fa-inr'></i> "+(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#nutri_estimate").attr('data-value'))));
                    $(".salary_deduction").attr("data-value",(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#nutri_total_premium").attr('data-value'))));
                    $("#nutri_estimate").attr('data-value','0');
                }               
                var flex_utilised =  parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#nutri_total_premium").val()) - parseInt(($("#nutri_estimate").attr('data-value') == '') ? 0 : $("#nutri_estimate").attr('data-value'));
              if(flex_utilised > $(".flex_alloted").attr('data-value'))
               {
                  $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Flex balance is not enough');
               }
               $(".flex_utilised").html("<i class='fa fa-inr'></i> "+flex_utilised);
               $(".flex_utilised").attr("data-value",flex_utilised);
               $("#nutri_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#nutri_total_premium").val()));
               $("#nutri_estimate").attr("data-value",parseInt($("#nutri_total_premium").val()));
               $("#nutri_estimate").attr("data-type",'F');
            }else
            {
              if($("#nutri_estimate").attr("data-type") == 'F')
                {
                    $(".flex_utilised").html("<i class='fa fa-inr'></i> "+(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#nutri_estimate").attr('data-value'))));
                    $(".flex_utilised").attr("data-value",(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#nutri_estimate").attr('data-value'))));
                    $("#nutri_estimate").attr('data-value','0');
                }                
                var salary_deduction =  parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#nutri_total_premium").val()) - parseInt(($("#nutri_estimate").attr('data-value') == '') ? 0 : $("#nutri_estimate").attr('data-value'));
                if(salary_deduction > $("#total_salary").val())
                {
                  $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Salary is not enough');
                }
                $(".salary_deduction").html("<i class='fa fa-inr'></i> "+salary_deduction);
                $(".salary_deduction").attr("data-value",salary_deduction);
                $("#nutri_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#nutri_total_premium").val()));
                $("#nutri_estimate").attr("data-value",parseInt($("#nutri_total_premium").val()));
                $("#nutri_estimate").attr("data-type",'S');
            }
        }); 
         //home_health
        $("#home_health_modal_btn").click(function (){
            $("#home_health_total_premium").val($("#home_health_estimate").attr("data-value"));
        });
        $("#submithome_health").click(function(){
            var deduction_type = $("input[name='home_health_type']:checked").val();
            var current_val = $("#home_health_estimate").attr('data-value');
            if(deduction_type == 'F')
            {
                if($("#home_health_estimate").attr("data-type") == 'S')
                {
                    $(".salary_deduction").html("<i class='fa fa-inr'></i> "+(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#home_health_estimate").attr('data-value'))));
                    $(".salary_deduction").attr("data-value",(parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#home_health_total_premium").attr('data-value'))));
                    $("#home_health_estimate").attr('data-value','0');
                }               
                var flex_utilised =  parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#home_health_total_premium").val()) - parseInt(($("#home_health_estimate").attr('data-value') == '') ? 0 : $("#home_health_estimate").attr('data-value'));
              if(flex_utilised > $(".flex_alloted").attr('data-value'))
               {
                  $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Flex balance is not enough');
               }
               $(".flex_utilised").html("<i class='fa fa-inr'></i> "+flex_utilised);
               $(".flex_utilised").attr("data-value",flex_utilised);
               $("#home_health_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#home_health_total_premium").val()));
               $("#home_health_estimate").attr("data-value",parseInt($("#home_health_total_premium").val()));
               $("#home_health_estimate").attr("data-type",'F');
            }else
            {
              if($("#home_health_estimate").attr("data-type") == 'F')
                {
                    $(".flex_utilised").html("<i class='fa fa-inr'></i> "+(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#home_health_estimate").attr('data-value'))));
                    $(".flex_utilised").attr("data-value",(parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#home_health_estimate").attr('data-value'))));
                    $("#home_health_estimate").attr('data-value','0');
                }                
                var salary_deduction =  parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#home_health_total_premium").val()) - parseInt(($("#home_health_estimate").attr('data-value') == '') ? 0 : $("#home_health_estimate").attr('data-value'));
                if(salary_deduction > $("#total_salary").val())
                {
                  $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Salary is not enough');
                }
                $(".salary_deduction").html("<i class='fa fa-inr'></i> "+salary_deduction);
                $(".salary_deduction").attr("data-value",salary_deduction);
                $("#home_health_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#home_health_total_premium").val()));
                $("#home_health_estimate").attr("data-value",parseInt($("#home_health_total_premium").val()));
                $("#home_health_estimate").attr("data-type",'S');
            }
        }); 

        $("#contact1-tab").click(function(){
                $(".payment_type:checked").map(function(){
                    if ($(this).closest('.slide-flex').find('.estimate_box').attr('data-value') !== '' && $(this).closest(".slide-flex").find(".estimate_box").attr('data-value') !== '0')
                    {
                      var myArray = ['benifit_14','benifit_12','benifit_11','benifit_15','benifit_10','benifit_13','benifit_16','benifit_17'];
                        if ($(this).closest('.slide-flex').attr('id') == 'benifit_14' || $(this).closest('.slide-flex').attr('id') == 'benifit_12' || $(this).closest('.slide-flex').attr('id') == 'benifit_11' || $(this).closest('.slide-flex').attr('id') == 'benifit_15' || $(this).closest('.slide-flex').attr('id') == 'benifit_10' || $(this).closest('.slide-flex').attr('id') == 'benifit_13' || $(this).closest('.slide-flex').attr('id') == 'benifit_16' || $(this).closest('.slide-flex').attr('id') == 'benifit_17')
                        {
                            // $("#non_core_list").append('<div class="row" style="border-bottom: 2px dashed silver;"><div class="col-md-6 mb-2"><span class="color-yel">'+$(this).closest(".slide-flex").find(".opa").html()+'</span></div><div class="col-md-3 mb-2"><span class="color-bul">Allocated</span></div> <div class="col-md-3 mb-2"><span class="color-bul">'+$(this).closest(".slide-flex").find(".opa").attr('data-value')+'</span></div></div>');
                            $("#non_core_list").empty();
                             if($(this).val() == 'f')
                              {
                                  $("#non_core_list").append('<tr data-benifit="'+$(this).closest(".slide-flex").attr("id")+'" data-amount="'+$(this).closest(".slide-flex").find(".estimate_box").attr('data-value')+'" data-si="'+$(this).closest(".slide-flex").find(".sum_insured_check:checked").val()+'" data-name="'+ $(this).closest(".slide-flex").find(".opa").text() +'"><td>'+ $(this).closest(".slide-flex").find(".opa").html() +' </td><td><span class="bor-dashed" id="sudexo_estimate_summary"> <i class="fa fa-inr"></i>'+$(this).closest(".slide-flex").find(".estimate_box").attr('data-value')+'</span></td><td>Wallet</td></tr>');
                              }else
                              {
                                 $("#non_core_list").append('<tr data-benifit="'+$(this).closest(".slide-flex").attr("id")+'" data-amount="'+$(this).closest(".slide-flex").find(".estimate_box").attr('data-value')+'" data-si="'+$(this).closest(".slide-flex").find(".sum_insured_check:checked").val()+'" data-name="'+ $(this).closest(".slide-flex").find(".opa").text() +'"><td>'+ $(this).closest(".slide-flex").find(".opa").html() +' </td><td><span class="bor-dashed" id="sudexo_estimate_summary"> <i class="fa fa-inr"></i>'+$(this).closest(".slide-flex").find(".estimate_box").attr('data-value')+'</span></td><td>Payroll</td></tr>');
                              }
                            // $("#non_core_list").append('<tr data-benifit="'+$(this).closest(".slide-flex").attr("id")+'" data-amount="'+$(this).closest(".slide-flex").find(".estimate_box").attr('data-value')+'" data-si="'+$(this).closest(".slide-flex").find(".sum_insured_check:checked").val()+'" data-name="'+ $(this).closest(".slide-flex").find(".opa").text() +'"><td>'+ $(this).closest(".slide-flex").find(".opa").html() +' </td><td><span class="bor-dashed" id="sudexo_estimate_summary"> <i class="fa fa-inr"></i>'+$(this).closest(".slide-flex").find(".estimate_box").attr('data-value')+'</span></td><td>payroll</td></tr>');
                        }
                        else
                        {
                          $("#core_list").empty();
                          if($(this).val() == 'f')
                              {
                                  $("#core_list").append('<tr data-benifit="'+$(this).closest(".slide-flex").attr("id")+'" data-amount="'+$(this).closest(".slide-flex").find(".estimate_box").attr('data-value')+'" data-si="'+$(this).closest(".slide-flex").find(".sum_insured_check:checked").val()+'" data-name="'+ $(this).closest(".slide-flex").find(".opa").text() +'"><td>'+ $(this).closest(".slide-flex").find(".opa").html() +' </td><td><span class="bor-dashed" id="sudexo_estimate_summary"> <i class="fa fa-inr"></i>'+$(this).closest(".slide-flex").find(".estimate_box").attr('data-value')+'</span></td><td>Wallet</td></tr>');
                              }else
                              {
                                 $("#core_list").append('<tr data-benifit="'+$(this).closest(".slide-flex").attr("id")+'" data-amount="'+$(this).closest(".slide-flex").find(".estimate_box").attr('data-value')+'" data-si="'+$(this).closest(".slide-flex").find(".sum_insured_check:checked").val()+'" data-name="'+ $(this).closest(".slide-flex").find(".opa").text() +'"><td>'+ $(this).closest(".slide-flex").find(".opa").html() +' </td><td><span class="bor-dashed" id="sudexo_estimate_summary"> <i class="fa fa-inr"></i>'+$(this).closest(".slide-flex").find(".estimate_box").attr('data-value')+'</span></td><td>Payroll</td></tr>');
                              }
                          
                        }
                        
                    }
                });
                if($("#sudexo_estimate").attr('data-value') && $("#sudexo_estimate").attr('data-value') != "0")
                {
                   $("#core_list").append('<tr data-benifit="benifit_1" data-amount="'+$("#sudexo_estimate").attr('data-value')+'" data-si="" data-name="Sodexo"><td >Sodexo</td><td><span class="bor-dashed" id="sudexo_estimate_summary"> <i class="fa fa-inr"></i>'+$("#sudexo_estimate").attr('data-value')+'</span></td></tr>');
                }

                var cor_list_num = 0;
                $("#coreAmt").html("0");
                $("#walletAmt").html("0");


                $("#core_list tr").each(function() {
                    cor_list_num += Number($("#coreAmt").html()) + Number($(this).attr("data-amount"));
                    $("#coreAmt").html(cor_list_num);
                });

                cor_list_num = 0;
                $("#non_core_list tr").each(function() {
                    cor_list_num += Number($("#walletAmt").html()) + Number($(this).attr("data-amount"));
                    $("#walletAmt").html(cor_list_num);
                });  

        });          
     // $("#flex_summary-tab").click(function (){
     //    $("#flex_list,#salary_list").html("");
     //     $(".payment_type:checked").map(function(){
     //       if($(this).closest(".col-lg-4").find(".bold-13").attr('data-value') !== '' && $(this).closest(".col-lg-4").find(".bold-13").attr('data-value') !== '0')
     //       {
     //            if($(this).val() == 'f')
     //            {
     //                $("#flex_list").append('<li data-benifit="'+$(this).closest(".col-lg-4").attr("id")+'" data-amount="'+$(this).closest(".col-lg-4").find(".bold-13").attr('data-value')+'" data-si="'+$(this).closest(".col-lg-4").find(".sum_insured_check:checked").val()+'" data-name="'+ $(this).closest(".col-lg-4").find(".opa").text() +'">'+ $(this).closest(".col-lg-4").find(".opa").html() +' <span class="bor-dashed" id="sudexo_estimate_summary"> <i class="fa fa-inr"></i>'+$(this).closest(".col-lg-4").find(".bold-13").attr('data-value')+'</span></li>');
     //            }else
     //            {
     //               $("#salary_list").append('<li data-benifit="'+$(this).closest(".col-lg-4").attr("id")+'" data-amount="'+$(this).closest(".col-lg-4").find(".bold-13").attr('data-value')+'" data-si="'+$(this).closest(".col-lg-4").find(".sum_insured_check:checked").val()+'" data-name="'+ $(this).closest(".col-lg-4").find(".opa").text() +'">'+$(this).closest(".col-lg-4").find(".opa").html()+' <span class="bor-dashed" id="sudexo_estimate_summary"> <i class="fa fa-inr"></i>'+$(this).closest(".col-lg-4").find(".bold-13").attr('data-value')+'</span></li>');              
     //            }
     //        }
     //     });
            // if($("#sudexo_estimate").attr('data-value') !== '0')
            // {
            //    $("#flex_list").append('<li data-benifit="benifit_1" data-amount="'+$("#sudexo_estimate").attr('data-value')+'" data-si="" data-name="Sodexo">Sodexo<span class="bor-dashed" id="sudexo_estimate_summary"> <i class="fa fa-inr"></i>'+$("#sudexo_estimate").attr('data-value')+'</span></li>');
            // }         
     // }) 
     $("#submit_flex_data").click(function (){

        if(Number($("#allotedAmt").html()) >= Number($("#coreAmt").html())) {
            alert("Wallet utilization exceeds Flex wallet");
            return;
        }

        var box = confirm("Are you sure want to submit your data ?");
        if (box == true) {
        var flex_data = [];
        var salary_data = [];
        $("#core_list tr").map(function (){
            flex_data.push({
                            benifit_type : $(this).attr('data-benifit'), 
                            amount : $(this).attr('data-amount'),
                            sum_insured: $(this).attr('data-si'),
                            name:  $.trim($(this).attr('data-name'))+",",
                           });
        });
        $("#non_core_list tr").map(function (){
            salary_data.push({
                            benifit_type : $(this).attr('data-benifit'), 
                            amount : $(this).attr('data-amount'),
                            sum_insured: $(this).attr('data-si'),
                            name: $(this).attr('data-name'),
                           });
        });
            $.ajax({
                 url: "/flexi_benifit/submit_flex_data",
                 type: "POST",
                 async: true,
                 data: {flex_data:flex_data,salary_data:salary_data},
                 dataType: "json",
                 success: function (response) {
                  $("#getCodeModal").modal("toggle");
                  $("#getCode").html('Records updated successfully !');
                }
            });         
        } else 
        {
          return false;
        }         
     });
        $("#show_summary").click(function(){          
            $("#flex_summary-tab").trigger('click');
        }); 
        $(".pc_member").click(function(){
            var numberChecked = $(".pc_member:checked").length;
            if(numberChecked > 2)
            {
                return false;
            }
        })
        $("#add_pc_members").click(function (){
           var relations = $(".pc_member:checked" )
                            .map(function() {
                              return $(this).val();
                            })
                            .get();
           var storage = window["localStorage"];
           storage.setItem('type',$("input[name='pc_type']:checked").val());
           storage.setItem('relations',relations);
           storage.setItem('policy_subtype_no','1');
           storage.setItem('parental_cover','2');
           storage.setItem('final_amount','3000');
           window.location = "/employee/policy_member_parent/";
        });
        $("#data").click(function(){
      window.location.reload();
  });
  // reimbursement 
     $.ajax({
                url: "/flexi_benifit/get_utilised_data",
                type: "POST",
                async: true,
                dataType: "json",
                success:function(data){
                    $.each(data.benifit_data,function(i,v){
                        if (v.master_flexi_benefit_id == 6) 
                        {
                            if (v.balance_amount == 0) 
                            {
                                 $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_bal").attr("data-value",v.final_amount);
                                 $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_bal").html("<i class='fa fa-inr'></i> "+v.final_amount);
                            }
                            else
                            {
                               amt = v.final_amount - v.reimbursement_ingst_amount;
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_bal").attr("data-value",amt);
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_bal").html("<i class='fa fa-inr'></i> "+amt);
                            }
                              $("#health_checkup").click(function(){
                                  window.location.href = "/employee/health_checkup_flexi_benefit";
                              });
                        }
                        else
                        {
                            $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_bal").attr("data-value",0);
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_bal").html("<i class='fa fa-inr'></i> "+"0");
                        }
                        if (v.master_flexi_benefit_id == 8) 
                        {
                            if (v.balance_amount == 0) 
                            {
                                 $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_bal_8").attr("data-value",v.final_amount);
                                 $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_bal_8").html("<i class='fa fa-inr'></i> "+v.final_amount);
                            }
                            else
                            {
                               amt = v.final_amount - v.reimbursement_ingst_amount;
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_bal_8").attr("data-value",amt);
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_bal_8").html("<i class='fa fa-inr'></i> "+amt);
                            }
                              $("#dental_care").click(function(){
                                  window.location.href = "/employee/dental_flexi_benefit";
                              });
                        }
                        else
                        {
                            $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_bal_8").attr("data-value",0);
                            $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_bal_8").html("<i class='fa fa-inr'></i> "+"0");
                        }
                        if (v.master_flexi_benefit_id == 11)
                        { 
                          if (v.balance_amount == 0) 
                          {
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_11").attr("data-value",v.final_amount);
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_11").html("<i class='fa fa-inr'></i> "+v.final_amount);
                          }
                          else
                          {
                             amt = v.final_amount - v.reimbursement_ingst_amount;
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_11").attr("data-value",amt);
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_11").html("<i class='fa fa-inr'></i> "+amt);
                            }
                            $("#elder_care").click(function(){
                                  window.location.href = "/employee/childcare_flexi_benefit";
                            });
                        }
                        else
                        {
                            $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_11").attr("data-value",0);
                            $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_11").html("<i class='fa fa-inr'></i> "+"0");
                        }
                        if (v.master_flexi_benefit_id == 12)
                        {
                            if (v.balance_amount == 0) 
                            {
                               $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_12").attr("data-value",v.final_amount);
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_12").html("<i class='fa fa-inr'></i> "+v.final_amount);
                            }
                            else 
                            {
                              amt = v.final_amount - v.reimbursement_ingst_amount;
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_12").attr("data-value",amt);
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_12").html("<i class='fa fa-inr'></i> "+amt);
                            }
                            $("#gym_care").click(function(){
                                  window.location.href = "/employee/gym_flexi_benefit";
                              });
                        }
                        else
                        {
                            $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_12").attr("data-value",0);
                            $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_12").html("<i class='fa fa-inr'></i>"+ "0");
                        }
                        if (v.master_flexi_benefit_id == 14)
                        {
                          if (v.balance_amount == 0) 
                            {
                             $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_7").attr("data-value",v.final_amount);
                             $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_7").html("<i class='fa fa-inr'></i> "+v.final_amount);
                            }
                            else
                            {
                              amt = v.final_amount - v.reimbursement_ingst_amount;
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_7").attr("data-value",amt);
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_7").html("<i class='fa fa-inr'></i> "+amt);
                            }
                              $("#wearable_care").click(function(){
                                  window.location.href = "/employee/wearabledevice_smartwatch_flexi_benefit";
                              });
                        }
                        else
                        {
                            $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_7").attr("data-value",0);
                            $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_7").html("<i class='fa fa-inr'></i> "+"0");
                        }
                         if (v.master_flexi_benefit_id == 10) 
                        {
                            if (v.balance_amount == 0) 
                            {
                             $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_10").attr("data-value",v.final_amount);
                            $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_10").html("<i class='fa fa-inr'></i> "+v.final_amount);
                            }
                            else
                            {
                              amt = v.final_amount - v.reimbursement_ingst_amount;
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_10").attr("data-value",amt);
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_10").html("<i class='fa fa-inr'></i> "+amt);
                            }
                              $("#yoga").click(function(){
                                  window.location.href = "/employee/yogazumba_flexi_benefit";
                              });
                        }
                        else
                        {
                            $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_10").attr("data-value",0);
                          $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_10").html("<i class='fa fa-inr'></i> "+"0");
                        }

                        if (v.master_flexi_benefit_id == 9) 
                        {
                            $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_9").attr("data-value",v.final_amount);
                            $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_9").html("<i class='fa fa-inr'></i> "+v.final_amount);
                        }
                         else
                        {
                            $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_9").attr("data-value",0);
                            $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_9").html("<i class='fa fa-inr'></i> "+"0");
                        }
                        if (v.master_flexi_benefit_id == 13)
                        {
                          if (v.balance_amount == 0) 
                            {
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_13").attr("data-value",v.final_amount);
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_13").html("<i class='fa fa-inr'></i> "+v.final_amount);
                            }
                            else
                            {
                              amt = v.final_amount - v.reimbursement_ingst_amount;
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_13").attr("data-value",amt);
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_13").html("<i class='fa fa-inr'></i> "+amt);
                            }
                              $("#cmp_care").click(function(){
                                  window.location.href = "/employee/cmp_flexi_benefit";
                              });
                        }
                        else
                        {
                            $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_13").attr("data-value",0);
                            $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_13").html("<i class='fa fa-inr'></i> "+"0");
                        }
                        if (v.master_flexi_benefit_id == 15)
                        {
                          if (v.balance_amount == 0) 
                            {
                             $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_bal_15").attr("data-value",v.final_amount);
                             $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_bal_15").html("<i class='fa fa-inr'></i> "+v.final_amount);
                            }
                            else
                            {
                              amt = v.final_amount - v.reimbursement_ingst_amount;
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_bal_15").attr("data-value",amt);
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_bal_15").html("<i class='fa fa-inr'></i> "+amt);
                            }
                              $("#vaccination_care").click(function(){
                                  window.location.href = "/employee/vaccination_immunization_flexi_benefit";
                              });
                        }
                        else
                        {
                            $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_bal_15").attr("data-value",0);
                            $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_bal_15").html("<i class='fa fa-inr'></i> "+"0");
                        }
                        if (v.master_flexi_benefit_id == 16)
                        {
                          if (v.balance_amount == 0) 
                            {
                             $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_16").attr("data-value",v.final_amount);
                             $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_16").html("<i class='fa fa-inr'></i> "+v.final_amount);
                            }
                            else
                            {
                              amt = v.final_amount - v.reimbursement_ingst_amount;
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_16").attr("data-value",amt);
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_16").html("<i class='fa fa-inr'></i> "+amt);
                            }
                              $("#nutri_care").click(function(){
                                  window.location.href = "/employee/nutrition_dietician_counselling_flexi_benefit";
                              });
                        }
                        else
                        {
                            $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_16").attr("data-value",0);
                            $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_16").html("<i class='fa fa-inr'></i> "+"0");
                        }
                        if (v.master_flexi_benefit_id == 17)
                        {
                          if (v.balance_amount == 0) 
                            {
                             $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_17").attr("data-value",v.final_amount);
                             $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_17").html("<i class='fa fa-inr'></i> "+v.final_amount);
                            }
                            else
                            {
                              amt = v.final_amount - v.reimbursement_ingst_amount;
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_17").attr("data-value",amt);
                              $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_17").html("<i class='fa fa-inr'></i> "+amt);
                            }
                              $("#homehealth_care").click(function(){
                                  window.location.href = "/employee/home_healthCare_flexi_benefit";
                              });
                        }
                        else
                        {
                            $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_17").attr("data-value",0);
                            $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box_17").html("<i class='fa fa-inr'></i> "+"0");
                        }

                    });
                }
              });
 $.ajax({
           url: "/get_parential_transaction_data_on_empid",
                 type: "POST",
                 dataType: "json",
                 async: true,
                  success: function (response) {
             if(response.length>0){
                    var count = 0;
                    $.each(response, function (index, value) {
                      count = value.fr_id.length -1;
                      deduction_type = value.deduction_type;
                      reltionship = value.fr_id;
                      transaction_id = value.employee_flexi_benefit_transaction_id;
                    });
                   
                    $("#employee_flexi_benefit_transaction_id").val(transaction_id);
                    var fr_id = reltionship.split(",");
                    $.each(fr_id,function(i,v){
                      if (v == 4) 
                      {
                       $("#customCheck2").prop('checked', true);
                      }
                      if (v == 5) 
                      {
                        $("#customCheck1").prop('checked', true);
                      }
                      if (v == 6) 
                      {
                        $("#customCheck3").prop('checked', true);
                      }
                      if (v == 7) 
                      {
                        $("#customCheck4").prop('checked', true);
                      }
                      else
                      {
                        $("#customCheck4").prop('checked', false);
                      }
                    })
                    if (count == 2) 
                    {
                        if (deduction_type == 'F') 
                        {
                          $('#pc_type2').prop('checked', true);
                        }
                        else
                        {
                          $('#pc_type1').prop('checked', true);
                        }
                        $("#add_pc_members").attr('style','display:none');
                        $("#add_parential").removeAttr('style','display:none');
                    }
                  } 
                }
        });
        $.ajax({
                url: "/get_policy_flexi_transaction_data",
                 type: "POST",
                 dataType: "json",
                 async: true,
                  success: function (response) {
                    $.each(response,function(i,v){
                        if (v.policy_sub_type_id == "1") 
                        {
                             $('#submitGHI').css('pointer-events','auto');
                        }
                        
                        if(v.policy_sub_type_id == "2")
                        {
                          $('#submitGPA').css('pointer-events','auto');
                        }
                        
                        if(v.policy_sub_type_id == "3")
                        {
                             $('#submitGLTA').css('pointer-events','auto');
                        }
                        
                    });
                      
                  }
        });
 $("#add_parential").click(function(){
             var deduction_type = $("input[name='pc_type']:checked").val();
           var employee_flexi_benefit_transaction_id = $("#employee_flexi_benefit_transaction_id").val();
            $.ajax({
                url: "/update_parential_transaction_data",
                type: "POST",
                data:{deduction_type:deduction_type,employee_flexi_benefit_transaction_id:employee_flexi_benefit_transaction_id},
               dataType: "json",
               async: true,
                 success:function(result){
                  
                 }
         });
         });

});

var slideIndex = 1;
         showDivs(slideIndex);
         
         function plusDivs(n, id) {
         showDivs(slideIndex += n, id);
         }
         
         function showDivs(n, id) {
         var i;
         var x = document.getElementsByClassName(id);

         if (n > x.length) {slideIndex = 1}
         if (n < 1) {slideIndex = x.length}
         for (i = 0; i < x.length; i++) {
         x[i].style.display = "none";  
         }
         try{
            x[slideIndex-1].style.display = "block"; 
         }catch(e) {} 
          
         }
function getPremiumCalc() {
    $.post("/flexi_benefit/getGtliTopUpcalc", {
      "sumValue":$("input[name='glta_si']:checked").val()
    }, function(e) {
      $("#glta_estimate").html('<i class="fa fa-inr"></i>'+ e.toLocaleString('en')+ '</span>');
      $("#glta_estimate").attr('data-value', e.toLocaleString('en'));

    });
  }

  $("#glta_si2").click(function() {
    getPremiumCalc();
  });

  $("#glta_si1").click(function() {
    getPremiumCalc();
  })