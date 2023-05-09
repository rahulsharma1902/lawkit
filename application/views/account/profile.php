<form class="hidden" id="frm-change-image-profile">
    <input type="file" class="custom-file-input" id="img-profile" name="image" accept="image/x-png,image/jpeg">
</form>
<div class="row">
    <div class="col-md-2">
        <div class="photo-profile">
            <div class="icon-user update-photo" <?= !empty($profile->photo) ? ' style="background-image:url(/assets/images/photo_customers/'.$profile->photo.')"' : NULL;?>>
                <?php if(empty($profile->photo)):?>
                    <i data-feather="user"></i>
                <?php endif;?>
            </div>
        </div>
        <?php if($profile->subscription_id != ""): ?>
            <p class="text-center text-success"><small>Cuenta PRO</small></p>
        <?php endif;?>
    </div>
    <div class="col-md-4">
        <form method="post" action="<?=base_url();?>account/profile">
            <input type="hidden" name="photo" id="photo">
            <div class="form-group">
                <label>Correo electrónico</label>
                <input type="email" class="form-control border-0 bg-light" name="email" id="email" value="<?=$profile->email;?>" readonly>
            </div>
            <div class="row">
                <div class="col-md">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" class="form-control border-0 bg-light" name="fname" id="fname"  value="<?=$profile->fname;?>">
                    </div>
                </div>
                <div class="col-md">
                    <div class="form-group">
                        <label>Apellidos</label>
                        <input type="text" class="form-control border-0 bg-light" name="lname" id="lname"  value="<?=$profile->lname;?>">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Dirección</label>
                <textarea class="form-control border-0 bg-light" name="address" id="address" rows="3"><?=$profile->address;?></textarea>
            </div>
            <div class="form-group">
                <label>Teléfono</label>
                <input type="phone" class="form-control border-0 bg-light" name="phone" id="phone" value="<?=$profile->phone;?>" maxlength="10">
            </div>
            <button class="btn btn-primary">Actualizar mis datos</button>
        </form>
    </div>
    <div class="col-md-6">
        <?php if($has_subs): ?>
        <p class="my-5 text-right">
            <small class="cursor-pointer cancel-subscription">Cancelar mi suscripción</a></small>
        </p>
    <?php endif; ?>
    </div>
</div>