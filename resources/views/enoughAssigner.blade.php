<!DOCTYPE html>
<html>
    <head>
        <title>3人以上にレビューされている割合</title>
    </head>
    <body>
        <h1>3人以上にレビューされている割合</h1>
        <?php foreach ($reviewAssigners as $data): ?>
            <h2><?php echo $data['listName']; ?></h2>
            <dl>
                <dt>カードの数 : </dt>
                <dd><?php echo $data['allCardsCount'] ?></dd>
                <dt>3人以上のレビューアがいるカード : </dt>
                <dd><?php echo $data['enoguhAssignerCardsCount'] ?></dd>
                <dt>3人以上にレビューされている割合 : </dt>
                <dd><?php echo $data['enoguhAssignerRetio'] ?>%</dd>
            </dl>
        <?php endforeach; ?>
    </body>
</html>
