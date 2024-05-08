<?= $this->extend('layouts/main'); ?>

<?= $this->section('results'); ?>

    <div class="card card-body panel panel-default">
        <div class="panel-body">
            <form method="post" action="/edit-product">
                <input type="hidden" name="id" value="<?=$product->id?>">
                <table>
                    <tr class="row">
                        <td class="key">OE</td>
                        <td class="val"><?= $product->OE ?></td>
                    </tr>
                    <tr class="row">
                        <td class="key">Desc</td>
                        <td class="val"><input type="text" name="desc" value="<?= $product->desc ?>"></td>
                    </tr>
                    <tr class="row">
                        <td class="key">Price</td>
                        <td class="val"><input type="text" name="price" value="<?= $product->price ?>"></td>
                    </tr>
                    <tr class="row">
                        <td class="key">Ignoring</td>
                        <td class="val">
                            <input type="checkbox" name="is_ignored" <?php echo ($product->is_ignored==1)? 'checked="checked"' : '' ?>>
                        </td>
                    </tr>
                </table>
                <div style="text-align: center">
                    <button type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>


    <style>
        .row {
            display: flex;
            padding-bottom: 12px;
        }

        .row .key {
            width: 20%;
        }

        .row .val {
            width: 100%;
        }

        .row .val input[type=text] {
            width: 100%;
        }

        table {
            width: 100%;
        }

        button {
            width: 80px;
            margin: auto;
        }
    </style>

<?= $this->endSection(); ?>