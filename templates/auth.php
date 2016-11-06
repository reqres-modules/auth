<style>body { background:#555; }</style>
<div class="modal in" style="display:block;" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Авторизация</h4>
      </div>
<form method="post">      
      <div class="modal-body">

         <input name="auth" type="hidden" value="login" />

          <div class="form-group">
            <label for="Input1">Логин</label>
            <input type="text" name="login" class="form-control input-lg" id="Input1"  value="<?= e($this->login) ?>" placeholder="Логин">
          </div>
          <div class="form-group">
            <label for="Input2">Пароль</label>
            <input type="password" name="password" class="form-control input-lg" placeholder="Пароль" id="Input2">
          </div>

      </div>
      <div class="modal-footer">
        <? if(!empty($this-> error)) : ?>
         <h4 class="text-danger pull-left"><?= e($this-> error)?></h4>
        <? endif ?>
        <button type="submit" class="btn btn-primary">Войти</button>
      </div>
</form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

