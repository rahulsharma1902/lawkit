<style>
    
.form-text {
  font-size: 12px;
  color: var(--red);
}
.is-invalid {
  border: 1px solid red;
  position: relative;
}
.is-invalid::after {
  content: \2297;
  position: absolute;
  right: 0;
}
.is-valid::after {
	content: ✔;
	position: absolute;
	float: right;
}
.my-3{
    margin:0 3rem;
}
</style>
<div class="my-3" style="padding: 50px;">
    <h4 class="text-primary title">Datos de facturación</h4>
    <div class="tab-pane">
        <div class="row mt-4 justify-content-md-center">
            <div class="col-12 col-md-8">
                <form id="frmDatosDFacturacion">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="">Nombre completo</label>
                            <input class="lawkit-input" type="text" name="nombreCompleto" id="nombreCompleto" maxlength="255">
                            <p class="form-text hidden" id="ft-nombreCompleto">El nombre es obligatorio</p>
                        </div>
                        <div class="col-md-6">
                            <label for="">RFC</label>
                            <input class="lawkit-input" type="text" name="rfc" id="rfc" maxlength="13">
                            <p class="form-text hidden" id="ft-rfc">El RFC es obligatorio</p>    
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="">Dirección</label>
                            <input class="lawkit-input" type="text" name="direccion" id="direccion" maxlength="255">
                            <p class="form-text hidden" id="ft-direccion">La direccion obligatoria</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="">Colonia</label>
                            <input class="lawkit-input" type="text" name="colonia" id="colonia" maxlength="255">
                            <p class="form-text hidden" id="ft-colonia">La colonia es obligatoria</p>
                        </div>
                        <div class="col-md-4">
                            <label for="">Municipio o Ciudad</label>
                            <input class="lawkit-input" type="text" name="municipioOCiudad" id="municipioOCiudad" maxlength="255">
                            <p class="form-text hidden" id="ft-municipioOCiudad">El municipio o ciudad es obligatorio</p>
                        </div>
                        <div class="col-md-4">
                            <label for="">Código Postal</label>
                            <input class="lawkit-input" type="text" name="codigoPostal" id="codigoPostal" maxlength="5">
                            <p class="form-text hidden" id="ft-codigoPostal">El código postal es obligatorio</p>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-md-2">
                        <button class="lawkit-btn bg-lk-blue-o2 save" type="button">Guardar</button>
                    </div>
                </div>
            </div>
            <?php
                if (!$datosf) { ?>
                <div class="col-md-4 m-auto">
                    <div class="no-data">
                        <svg xmlns="http://www.w3.org/2000/svg" width="130" height="auto" fill="rgba(255, 0, 0, 0.43)" class="bi bi-exclamation-circle" viewBox="0 0 16 16">
                          <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                          <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
                        </svg>
                        <h5>¡No hay datos guardados!</h5>
                    </div>
                </div>
                <?php } else { ?>
            <div class="col-md-4">
                <ul class="list-group">
                    <?php foreach($datosf as $dato): ?>
                    <li class="list-group-item">
                        <div class="card">
                            <div class="card-header">
                                <p>
                                    <?php if($dato->actual == 1):?>
                                        <strong><?=$dato->nombre;?></strong>
                                    <?php else: ?>
                                        <?=$dato->nombre;?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="card-body">
                                <p><?=$dato->direccion . " " . $dato->colonia . " " . $dato->municipio . " " . $dato->cp;?></p>
                                <?php if($dato->actual == 0):?>
                                    <div class="row">
                                        <div class="col my-3">
                                            <div class="float-right">
                                                <button class="lawkit-btn bg-lk-blue-o2 mark" data-id="<?=$dato->id;?>">Usar esta dirección</button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php } ?>
        </div>
    </div>

</div>
<script type="text/javascript">
    $(document).ready(function(){
        //RFC
        $('input#rfc').on('keyup', function(){
            $(this).val($(this).val().toUpperCase())
        })

        $('.mark').on('click', function(){
            const ID = $(this).data('id')
            $.ajax({
                type:'GET',
                url:`/account/datos?id=${ID}`,
                dataType:'json',
                beforeSend: function(){
                    $(this).addClass('disabled p-none').html('<i>Estableciendo...</i>')
                }, success: function(response){
                    if(response.error){
                        Swal.fire({
                            text: response.message
                        })
                        return;
                    } else {
                        Swal.fire({
                            text: response.message
                        })
                        setTimeout(function(){
                            $('.save').removeClass('disabled p-none').html('Usar esta dirección')
                            window.location=window.location
                        }, 1500)
                    }
                }
            })
        })

        $('.save').on('click', function(){
            if($('input#nombreCompleto').val().length <= 0){
                $('input#nombreCompleto').addClass('is-invalid');
                $('p#ft-nombreCompleto').removeClass('hidden');
                return;
            } else {
                $('input#nombreCompleto').removeClass('is-invalid').addClass('is-valid');
                $('p#ft-nombreCompleto').addClass('hidden');
            }
            if($('input#rfc').val().length <= 0){
                $('input#rfc').addClass('is-invalid');
                $('p#ft-rfc').removeClass('hidden');
                return;
            } else {
                $('input#rfc').removeClass('is-invalid').addClass('is-valid');
                $('p#ft-rfc').addClass('hidden');
            }
            if($('input#direccion').val().length <= 0){
                $('input#direccion').addClass('is-invalid');
                $('p#ft-direccion').removeClass('hidden');
                return;
            } else {
                $('input#direccion').removeClass('is-invalid').addClass('is-valid');
                $('p#ft-direccion').addClass('hidden');
            }
            if($('input#colonia').val().length <= 0){
                $('input#colonia').addClass('is-invalid');
                $('p#ft-colonia').removeClass('hidden');
                return;
            } else {
                $('input#colonia').removeClass('is-invalid').addClass('is-valid');
                $('p#ft-colonia').addClass('hidden');
            }
            if($('input#municipioOCiudad').val().length <= 0){
                $('input#municipioOCiudad').addClass('is-invalid');
                $('p#ft-municipioOCiudad').removeClass('hidden');
                return;
            } else {
                $('input#municipioOCiudad').removeClass('is-invalid').addClass('is-valid');
                $('p#ft-municipioOCiudad').addClass('hidden');
            }
            if($('input#codigoPostal').val().length <= 0){
                $('input#codigoPostal').addClass('is-invalid');
                $('p#ft-codigoPostal').removeClass('hidden');
                return;
            } else {
                $('input#codigoPostal').removeClass('is-invalid').addClass('is-valid');
                $('p#ft-codigoPostal').addClass('hidden');
            }
            $.ajax({
                type:'POST',
                data:$('#frmDatosDFacturacion').serialize(),
                dataType:'json',
                url:'/account/facturacion',
                beforeSend:function(){
                    $('.save').addClass('disabled p-none').html('<i>Guardando...</i>')
                }, success: function(response){
                    if(response.error){
                        Swal.fire({
                            text: response.message
                        })
                        return;
                    } else {
                        Swal.fire({
                            text: response.message
                        })
                        setTimeout(function(){
                            $('.save').removeClass('disabled p-none').html('Guardar')
                            window.location=window.location
                        }, 1500)
                    }
                }
            })
        })
    })
</script>