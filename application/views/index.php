<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="<?=base_url();?>assets/lawkitlandingpage/css/style.css" rel="stylesheet">
    <title>Lawkit Home</title>
  </head>
  <body>

<header class="site-header">
  <div class="container">
    <div class="header-wrapper">
      <a href="<?=base_url('');?>" class="brand">
        <img src="<?=base_url();?>assets/lawkitlandingpage/images/white-logo.png">
      </a>
      <button class="menu-toggle">
        <span class="bar bar-1"></span>
        <span class="bar bar-2"></span>
        <span class="bar bar-3"></span>
      </button>
      <div class="header-menu-wrapper">
        <div class="header-menu">
          <a href="<?=base_url('account/signin');?>" class="cta-round cta-round-light">Iniciar Sesión <i class="fa-regular fa-angle-right"></i></a>
          <a href="#" class="cta-round cta-round-blank" data-bs-toggle="modal" data-bs-target="#exampleModal">Contáctanos <i class="fa-regular fa-angle-right"></i></a>
        </div>
      </div>
    </div>
  </div>
</header>

<div class="home-banner" style="background-image: url(<?=base_url();?>assets/lawkitlandingpage/images/awseg.svg);">
  <div class="container">
    <div class="inner-section">
      <div class="inner-banner">
        <div class="heading">
          <h1>Litiga con <br> tecnología. <br><span class="blue">Es hora.</span></h1>
        </div>
        <div class="banner-image">
          <img src="<?=base_url();?>assets/lawkitlandingpage/images/computer-graphiuc.png" alt="loading">
        </div>
        <div class="description">
          <h3>LawKit te da dos herramientas revolucionarias para tus litigios:</h3>
          <div class="desc-row row">
            <div class="desc-col col-lg-6 col-md-6">
              <p><strong>1. Buscador de sentencias.</strong> Busca más de 250k sentencias por palabra clave: artículos, concepto, nombres de autoridades o cualquier otro que se te ocurra. Si está en una sentencia pública, aquí lo encontrarás.</p>
            </div>
            <div class="desc-col col-lg-6 col-md-6">
              <p><strong>2. Calculadora de términos:</strong> Deja de contar tus plazos y términos a mano. Nuestra calculadora lo hace por ti.</p>
            </div>
          </div>
        </div>
        <div class="btn-wrapper">
          <a href="<?=base_url('account/signup');?>" class="cta-round cta-round-blue">Pruébalo ahora <i class="fa-regular fa-angle-right"></i></a>
        </div>
      </div>
    </div>
  </div>
</div>

<footer class="text-center page-footer">
  <div class="container">
    <p>© 2023 Lawkit</p>
  </div>
</footer>

<form id="FrmContact" action="javascript:;" enctype="multipart/form-data" method="POST" accept-charset="utf-8">
      <div class="modal modal-sheet py-5" tabindex="-1" role="dialog" id="exampleModal">
        <div class="modal-dialog" role="document">
          <div class="modal-content rounded-4 shadow">
            <div class="modal-header border-bottom-0">
              <h1 class="modal-title fs-5">Contacto</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-0">
                <div class="row">
                    <label id="texto"></label>
                </div>
                <div class="row">
                    <!-- Name -->
                    <div class="col-md-6 mb-2">
                       <input class="form-control main" name="name" type="text" placeholder="Nombre" required>
                    </div>
                    <!-- Email -->
                    <div class="col-md-6 mb-2">
                       <input class="form-control main" name="email" type="email" placeholder="Correo electrónico" required>
                    </div>
                    <!-- subject -->
                    <div class="col-md-12 mb-2">
                       <input class="form-control main" name="subject" type="text" placeholder="Asunto" required>
                    </div>
                    <!-- Message -->
                    <div class="col-md-12 mb-2">
                       <textarea class="form-control main" name="message" rows="10" placeholder="Mensaje"></textarea>
                    </div>
                </div>
                <div class="row">
                    <span class="msg-error error"></span>
                    <div id="recaptcha" class="g-recaptcha" data-sitekey="6LfQAsQkAAAAANOBkuydjPpC-8Pq26p5ZJXacT-G"></div> 
                </div>
            </div>
            <div class="modal-footer flex-column border-top-0">
              <button type="submit" class="btn btn-lg btn-primary w-100 mx-0 mb-2">Enviar</button>
              <button type="button" class="btn btn-lg btn-light w-100 mx-0" data-bs-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>
    </form>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha512-pumBsjNRGGqkPzKHndZMaAG+bir374sORyzM3uulLV14lN5LyykqNk8eEeUlUkB3U0M4FApyaHraT65ihJhDpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> -->

<script src="<?=base_url();?>assets/js/jquery.min.js"></script>
    <script src="<?=base_url();?>assets/js/bootstrap.bundle.min.js"></script>
    <script async src='https://www.google.com/recaptcha/api.js?hl=es'></script>
    
    <script>
    $(document).ready(function(){
        $("#FrmContact").submit(function( event ) {
            var parametros = $(this).serialize();
        	 $.ajax({
        		type: "POST",
        		url: "<?=base_url();?>account/post_contact",
        		data: parametros,
        		beforeSend: function(objeto){
        			//$("#LoginResult").html('<div class="alert alert-warning" role="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><center><strong>Mensaje: Cargando...</strong></center></div>');
        		},
        		success: function(datos){
            		$("#texto").html(datos);
            		if(datos.search( 'success' ) > 0){
                        $("#FrmContact").trigger("reset");
            		}
                    grecaptcha.reset();
        	    }
        	});
            event.preventDefault();
        });
    });
    </script>
      
<script>
   $(document).ready(function(){
 $('.menu-toggle').on('click', function() {
    $('.header-menu-wrapper').slideToggle();
  });
}); 
</script>


  </body>
</html>