<?php
$rowTop = [
    ['file' => 'microsoft.jpg',     'name' => 'Microsoft'],
    ['file' => 'apple.jpg',         'name' => 'Apple'],
    ['file' => 'samsung.jpg',       'name' => 'Samsung'],
    ['file' => 'meta.jpg',          'name' => 'Meta'],
    ['file' => 'netflix.jpg',       'name' => 'Netflix'],
    ['file' => 'mercedes-benz.jpg', 'name' => 'Mercedes-Benz'],
    ['file' => 'bmw.jpg',           'name' => 'BMW'],
];
$rowBottom = [
    ['file' => 'adidas.jpg',    'name' => 'Adidas'],
    ['file' => 'nike.jpg',      'name' => 'Nike'],
    ['file' => 'cocacola.jpg',  'name' => 'Coca-Cola'],
    ['file' => 'starbucks.jpg', 'name' => 'Starbucks'],
    ['file' => 'youtube.jpg',   'name' => 'YouTube'],
    ['file' => 'insta.jpg',     'name' => 'Instagram'],
];
$all = array_merge($rowTop, $rowBottom);

$renderTrack = static function (array $row, string $modifier, bool $eager) {
    ?>
    <div class="logos__viewport" data-marquee>
      <ul class="logos__row logos__track <?= $modifier ?>" role="list" aria-hidden="true">
        <?php foreach ($row as $b): ?>
          <li class="logo-box">
            <img src="/assets/img/logos/<?= htmlspecialchars($b['file'], ENT_QUOTES) ?>"
                 alt=""
                 loading="<?= $eager ? 'eager' : 'lazy' ?>"
                 decoding="async"
                 width="140" height="48">
          </li>
        <?php endforeach; ?>
        <?php foreach ($row as $b): ?>
          <li class="logo-box" aria-hidden="true">
            <img src="/assets/img/logos/<?= htmlspecialchars($b['file'], ENT_QUOTES) ?>"
                 alt=""
                 loading="lazy"
                 decoding="async"
                 width="140" height="48">
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php
};
?>
<section class="logos" aria-labelledby="logos-heading" data-reveal="up">
  <div class="container">
    <h3 id="logos-heading" class="h3 logos__title" data-reveal="up">Trusted by procurement leaders worldwide</h3>

    <?php $renderTrack($rowTop, 'logos__track--fast', true); ?>
    <?php $renderTrack($rowBottom, 'logos__track--reverse logos__track--slow', false); ?>

    <ul class="visually-hidden" aria-label="Trusted by these enterprises">
      <?php foreach ($all as $b): ?>
        <li><?= htmlspecialchars($b['name'], ENT_QUOTES) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
