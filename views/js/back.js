/**
 * NOTICE OF LICENSE
 *
 * This file is licensed under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the license agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    YOURAI
 * @copyright 2016-2024 YOURAI
 * @license LICENSE.txt
 */

$(document).ready(function () {
    $(document).on('click', '.onboarding-process-action .btn-processed', function () {
        $('.loader').addClass('show');
        $('.your-onbaording-process').css('opacity', 0.5);

        $.ajax({
            url: shop_register_proxy_url,
            type: 'POST',
            cache: false,
            dataType: 'json',
            success: function (response) {
                if (response.success === true) {
                    $('.your-onbaording-process .alert-success').html('Account Created.').removeClass('ds-none');
                    $('.your-onbaording-process').css('opacity', 1);
                    $('.loader').removeClass('show');
                    location.reload();
                } else if (response.success === false) {
                    var errorMessage = '';
                    if (response.errors) {
                        $.each(response.errors, function (key, value) {
                            if (Array.isArray(value)) {
                                value.forEach(function (msg) {
                                    errorMessage += msg;
                                });
                            } else {
                                errorMessage += value;
                            }
                        });
                    } else if (response.message) {
                        errorMessage = response.message;
                    } else {
                        errorMessage = 'API Error'
                    }

                    $('.your-onbaording-process .alert-danger').html(errorMessage).removeClass('ds-none');
                    $('.your-onbaording-process').css('opacity', 1);
                    $('.loader').removeClass('show');
                }
            },
            error: function (xhr, status, error) {
                $('.your-onbaording-process .alert-danger').html(error).removeClass('ds-none');
                $('.your-onbaording-process').css('opacity', 1);
                $('.loader').removeClass('show');
            }
        });
    });
    $.ajax({
        url: dashboard,
        type: 'POST',
        cache: false,
        dataType: 'html',
        success: function (response) {
            $('#yourDashboardConfiguration').html(response);
        }
    });


});