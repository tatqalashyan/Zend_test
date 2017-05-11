$(document).ready(function () {
    $('#convert').click(function (e) {
        e.preventDefault();
        if(isInt($('#amount').val()) || isFloat($('#amount').val())) {

                var form = $('#currency-form').serializeArray();
                console.log(form);
                jQuery.ajax({
                    url: "index/ajax",
                    type: "POST",
                    dataType: 'json',
                    data: form,
                    success: function (data) {
                        $('#result tr:first-child').after('<tr><td>' + data.currency_in + '</td><td>' + data.amount + '</td><td>' + data.currency_out + '</td><td>' + data.result + '</td></tr>');
                        $('#result tr:last-child').remove();
                    },
                    error: function () {
                        alert("fail :(");
                    }
                });

        }else{
            alert('wrong');
        }

    })


});

function isInt(n) {
    return n % 1 === 0;
}

function isFloat(n){
    return Number(n) === n && n % 1 !== 0;
}