





            <div class="container my-5">
                <div class="row">
                    <div class="col-sm-6" data-aos="fade-right">
                    <h2 class="heading">Gadgets Insurance</h2>
                       
                    <p>Protect your valuable gadgets with our comprehensive gadget insurance. From smartphones and laptops to tablets and cycles, our policy covers accidental damage, theft, and loss so you can know that your devices are always secure. With affordable premiums and an easy claims process, you can enjoy your gadgets without worrying about the unexpected.

                        <br>  <b>Get your gadget insurance today!</b></p>
                    <div class="home-img mt-5">
                        <img src="/assets/gadget/img/home-1.png">
                    </div>
                    </div>
                    <div class="col-sm-6" data-aos="fade-left">
                        <div class="form">
                            <form>
                                    <h5>Tell us about yourself</h5>
                                        <div class="progress">
                                            <div class="progress-bar bg-orange" style="width:40%">                                    
                                            </div>                                    
                                        </div>
                                    <div class="form-group">
                                        <label>Full Name</label>
                                        <input type="text" id="fullname"class="form-control fullname details" placeholder="Enter Full Name">
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="text" class="form-control email details " placeholder="Enter Email ID">
                                    </div>
                                    <div class="form-group">
                                        <label>Mobile No</label>
                                        <input type="text" maxlength="10" pattern="\d*" class="form-control  mobile details " placeholder="Enter Mobile No.">
                                    </div>
                                     <div class="form-group">
                                        <label>Gender</label><br>
                                         <input type="radio" id="male" class="details" checked name="gender" value="male">
                                         <label for="html">Male</label>
                                         <input type="radio" id="female" class="details" name="gender" value="female">
                                         <label for="css">Female</label>
                                    </div>
                                    <div class="next-btn">
                                        Next <button class="submitLead" type="button" ><i class="fa fa-long-arrow-right"></i></button>
                                   </div>
                            </form>                           
                        </div>                        
                    </div>   
                </div>
                <div class="">
                    <img src="assets/gadget/img/vector1.png" class="vector-img">
                </div>
            </div>
    </div>    
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
       AOS.init({
        duration: 0,
        });
       $('#fullname').keyup(function(){
         //  this.value = this.value.toUpperCase();
           $('.details').closest('div').find('span.error').remove();
           var str = $(this).val();
           var regName = /^[a-zA-Z]+ [a-zA-Z]+$/;
           if(!regName.test(str)){
               $('.fullname').closest('div').append('<span class="error">Please enter full name</span>');
           }
           const mySentence = str;
           const words = mySentence.split(" ");
           for (let i = 0; i < words.length; i++) {
               words[i] = words[i][0].toUpperCase() + words[i].substr(1);
           }
          var name= words.join(" ");
           $('.fullname').val(name)
          // alert(yourtext.substr(0, 1).toUpperCase() + yourtext.substr(1));
       });
       $('.email').keyup(function(){
           $('.details').closest('div').find('span.error').remove();
           var email = $.trim($('.email').val());
           if (email == '') {

               $('.email').closest('div').append('<span class="error">Email ID is required</span>');
           }

           var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

           if (email.match(validRegex)) {


           } else {

               $('.email').closest('div').append('<span class="error">Valid Email is required</span>');
           }
       });
       $('.mobile').keyup(function(){
           $('.details').closest('div').find('span.error').remove();
           var mobile = $.trim($('.mobile').val());
           if (mobile == '') {


               $('.mobile').closest('div').append('<span class="error">Mobile no. is required</span>');
           } else if (mobile.length < 10) {

               $('.mobile').closest('div').append('<span class="error">Mobile no. should be 10 digits only</span>');
           } else {
               var filter = /[6-9]{1}[0-9]{9}/;
               if (!filter.test(mobile)) {
                   $('.mobile').closest('div').append('<span class="error">Enter valid 10 digit no. starting from 6 to 9</span>');
               }
           }
       });

       $('.submitLead').on('click', function(event) {

           $('.details').closest('div').find('span.error').remove();

           var name = $.trim($('.fullname').val());
           var mobile = $.trim($('.mobile').val());
           var email = $.trim($('.email').val());
           var gender = $.trim($('input[name="gender"]:checked').val());
           //var re = new RegExp('^[6-9][0-9]{9}$');
           $('.details').closest('div').find('span.error').remove();
           hasError = false;

           if (name == '') {

               hasError = true;
               $('.fullname').closest('div').append('<span class="error">Name is required</span>');
           }

           if (mobile == '') {


               $('.mobile').closest('div').append('<span class="error">Mobile no. is required</span>');
           } else if (mobile.length < 10) {

               hasError = true;
               $('.mobile').closest('div').append('<span class="error">Mobile no. should be 10 digits only</span>');
           } else {
               var filter = /[6-9]{1}[0-9]{9}/;
               if (!filter.test(mobile)) {
                   hasError = true;
                   $('.mobile').closest('div').append('<span class="error">Enter valid 10 digit no. starting from 6 to 9</span>');
               }
           }

           if (email == '') {

               hasError = true;
               $('.email').closest('div').append('<span class="error">Email ID is required</span>');
           }

           if (email != '') {
               var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
               if (!regex.test(email)) {
                   hasError = true;
                   $('.email').closest('div').append('<span class="error">Enter valid email address</span>');
               }

           }

           if (gender == '') {

               hasError = true;
               $('input[name="gender"]:checked').closest('div').append('<span class="error">Gender is required</span>');
           }

           if (hasError) {
               event.stopImmediatePropagation();
           }


           if (!hasError) {

               data = {};
               data.fullname = name;
               data.email = email,
                   data.mobile = mobile;
               data.gender = gender;

               $.ajax({

                   url: '/GadgetInsurance/submitLead',
                   method: 'POST',
                   data: data,
                   async: false,
                   cache: false,
                   success: function(response) {

                       var res = JSON.parse(response);
                        if(res.code == 201){
                            $('.fullname').closest('div').append('<span class="error">'+res.msg+'</span>');
                            return;
                        }
                       /*$('#hidden_lead_id').val(res.data.lead_id);
                       $('#hidden_cust_id').val(res.data.customer_id);
*/
                       location.href = "/GadgetInsurance/select?Lead="+res.data.lead_id+"&customer_id="+res.data.customer_id;

                   }
               });
           }
           return false;
       });
      </script>
</body>
</html>


