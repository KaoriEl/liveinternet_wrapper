require('./bootstrap');

    let username_auth = 'agronom';
    let password_auth = '73C$@FNZ5+s?zb3j';
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('.wrapper').on('click',function(){
        let site_id = this.id;
        $.ajax({
            method: "POST",
            url: "/getSite/"+site_id,
            dataType: 'json',
            headers: {
                "Authorization": "Basic " + btoa(username_auth + ":" + password_auth)
            },
            success: function (data) {

                $.each(data, function (index) {
                    $('#SiteUrl').val(data.site_url);
                })

            }
        })

    });


