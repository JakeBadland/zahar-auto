<?= $this->extend('layouts/main'); ?>

<?= $this->section('cargo-filter'); ?>

<style>
    .cargo-container{
        width: 600px;
        margin: auto;
        text-align: center;
    }
    textarea {
        width: 100%;
        height: 80vh;
        margin-bottom: 10px;
    }
</style>

<div class="cargo-container">
    <form action="cargo-list" method="POST">
        <textarea name="filters"><?=$filters?></textarea>
        <button type="submit">Save</button>
        <button><a href="/">Back</a></button>
    </form>
</div>


<?= $this->endSection(); ?>
