jQuery(document).ready(function($) {
    $('#js_submit_btn').click(function (e) {
        let name = $('#name').val();
        let password = $('#surname').val();
        let email = $('#email').val();

        e.preventDefault();
        $.ajax({
            url:  MYSCRIPT.ajaxUrl,
            data: {
                action: 'my_ajax_request',
                name: name,
                password: password,
                email: email,
            },
            method: 'POST',
            success: function (response) {
                console.log(response);
            },
            error: function (error) {
                console.log(error)
            }
        })
     })
    }
)
