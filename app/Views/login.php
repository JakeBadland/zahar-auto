<?= $this->extend('layouts/main'); ?>

<?= $this->section('login'); ?>

<div class="container login-form">
    <form action="/login" method="post">
        <input type="text" name="login" placeholder="Login"><br/><br/>
        <input type="password" name="password" placeholder="Password"><br/><br/>
        <input type="submit" value="Login">
    </form>
</div>

<?= $this->endSection(); ?>