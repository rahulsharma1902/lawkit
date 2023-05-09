var base_url = window.location.origin;
$(document).ready(function() {
    feather.replace();
    new WOW().init();

    if ($('#toast-parent').length > 0) {
        setTimeout(function() {
            $('#toast-parent').animate({ opacity: 0 }, 7000, function() {
                $('#toast-parent').remove();
            });
        }, 3000)
    }
    $('.close-toast').on('click', function() {
        $('#toast-parent').remove();
    })

    $('#sidebarToggleTop, #sidebarToggle').on('click', function() {
        $('body').toggleClass('sidebar-toggled');
        $('#accordionSidebar').toggleClass('toggled');
    })

    var intervalId = setInterval(function() {
        $.checkCustomerSession();
    }, 10000)


    $.checkCustomerSession = function() {
        $.ajax({
            url: `${base_url}/account/checkcustomersession`,
            type: 'GET',
            dataType: 'json',
            success: function(is) {
                if (is.error) {
                    clearInterval(intervalId);
                    window.location = window.location;
                }
            }
        })
    }









});


var ACCOUNT = {
    signup: function() {
        $('button.create').on('click', function() {
            var pass = $('input#password').val();
            var passc = $('input#password_confirm').val();
            if (pass !== passc) {
                swal('Error', 'Las contraseñas no coinciden', 'error');
                return false;
            }
            $('form#frm-signup').submit();
        })
    },
    profile: function() {
        $('input#phone').mask('000-000-0000')
        $('div.photo-profile').on('click', function() {
            $('input#img-profile').trigger('click');
        });
        $('input#img-profile').on('change', function() {
            var thisForm = $('#frm-change-image-profile')[0];
            var data = new FormData(thisForm);
            data.append('image', $('#img-profile')[0].files[0])
            $.ajax({
                    url: base_url + '/account/upload_photo',
                    type: 'POST',
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    data: data,
                    cache: false,
                    beforeSend: function() {
                        let loader = '<i data-feather="loader" class="rotating"></i>';
                        $('.icon-user').empty().append(loader)
                        feather.replace();
                    },
                    success: function(data) {
                        if (data.error && data.error !== "") {
                            swal('Error', 'Ocurrió un error al actualizar tu imagen de perfil.', 'error');
                            return;
                        }
                        $('input#photo').val(data.filename);
                        $('.icon-user').empty().addClass('update-photo').css('background-image', 'url(/assets/images/photo_customers/' + data.filename + ')');
                        $('.img-profile').attr('src', base_url + '/assets/images/photo_customers/' + data.filename);
                        $('#update_profile_form').submit();
                    }
                })
                .fail(function() {
                    swal('Error', 'Ocurrió un error al actualizar tu imagen de perfil.', 'error');
                    let loader = '<i data-feather="user"></i>';
                    $('.icon-user').empty().append(loader)
                    feather.replace();
                })
        })
    },
    cancelSubscription: function() {
        $('.cancel-subscription').on('click', function() {
            Swal.fire({
                title: 'Cancelar la suscripción',
                text: "¿Confirmas que deseas cancelar tu suscripción y perder los accesos a las herramientas, calculadora de término, buscador de sentencias y constructor de contratos?",
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí',
                cancelButtonText: "No",
                allowEscapeKey: false,
                allowOutsideClick: false
            }).then((result) => {
                if (result.dismiss != "cancel") { //si acepta
                    window.location = base_url + '/account/cancel_subscription';
                }
            })
        })
    }
}
var DASHBOARD = {
    init: function() {
        $('.counter').counterUp({
            delay: 10,
            time: 1000
        });
    },
    copyInfo: function() {
        $('button#copytoclipboard').on('click', function() {
            var copyText = document.getElementById("cb");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");
            swal('Aviso', 'La información ha sido copiada, usa Ctrl + V para pegar', 'info');
        })
    },
    slides: function() {
        $('#carousel-calendar').carousel({
            interval: false,
            ride: false,
            wrap: false
        });
        $('.chevrons-left').on('click', function() {
            $('#carousel-calendar').carousel('prev')
        })
        $('.chevrons-right').on('click', function() {
            $('#carousel-calendar').carousel('next')
        })
    },
    navbarHeader: function() {
        $('.g_buscador').attr('href', 'https://buscador.lawkit.mx?token=' + localStorage.getItem('token'));
        $('.g_calculadora').attr('href', 'https://buscador.lawkit.mx?token=' + localStorage.getItem('token'));
        $('.g_contratos').attr('href', 'https://buscador.lawkit.mx?token=' + localStorage.getItem('token'));
    },
    toggleCalendar: function() {
        $(".calendar-btn").on("click", function() {
                $(".calendar-wrapper").toggleClass("calendar-toggled")

                if (!$(".calendar-wrapper").hasClass('calendar-toggled')) {
                    $("#main-left").removeClass('col-md-9').addClass('col-md-10')
                    $("#main-row").removeClass('justify-content-left').addClass('justify-content-md-center')
                } else {
                    $(".calendar-btn").css('display', 'none')
                    $("#main-left").removeClass('col-md-10').addClass('col-md-9')
                    $("#main-row").removeClass('justify-content-md-center').addClass('justify-content-left')
                }
            }),
            $(".close-calendar").on("click", function() {
                $(".calendar-wrapper").removeClass("calendar-toggled")
                $(".calendar-btn").css('display', 'block')
                $("#main-left").removeClass('col-md-9').addClass('col-md-10')
                $("#main-row").removeClass('justify-content-left').addClass('justify-content-md-center')
            })
    },
    deleteCard: function() {
        /* $('#delete-card-btn').on('click', function() {
            var card_id = $(this).attr('card-id');
            Swal.fire({
                title: 'Eliminar tarjeta',
                text: "¿Confirmas que deseas eliminar esta tarjeta?",
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí',
                cancelButtonText: "No",
                allowEscapeKey: false,
                allowOutsideClick: false
            }).then((result) => {
                
            })
        }) */
    },
    saveCard: function() {
        $('#save-card-btn').on('click', function() {
            console.log("Saved")
        });
    },
    toggleDropdownMobile: function() {
        $('#userDropdown').on('click', function() {
            $('.dropdown-content').toggleClass('show-dropdown');
        });
    }
}

$(document).ready(ACCOUNT.signup);
$(document).ready(ACCOUNT.profile);
$(document).ready(ACCOUNT.cancelSubscription)
$(document).ready(DASHBOARD.init)
$(document).ready(DASHBOARD.copyInfo)
$(document).ready(DASHBOARD.slides)
$(document).ready(DASHBOARD.navbarHeader)
$(document).ready(DASHBOARD.toggleCalendar)
$(document).ready(DASHBOARD.toggleDropdownMobile)
$(document).ready(DASHBOARD.deleteCard)
$(document).ready(DASHBOARD.saveCard)