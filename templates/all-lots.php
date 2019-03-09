<div class="container">
    <section class="lots">
        <h2>Все лоты в категории «<?=$present_category['name']; ?>»</h2>
        <?php if (empty($lots)): ?>
        <p><?=$message; ?></p>
        <?php else: ?>
    <ul class="lots__list">
    <?php foreach ($lots as $lot): ?>
    <li class="lots__item lot">
        <div class="lot__image">
            <img src="../<?=$lot['image']; ?>" width="350" height="260" alt="Изображение лота">
        </div>
        <div class="lot__info">
            <span class="lot__category"><?=$lot['category']; ?></span>
            <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?=$lot['lot_id']; ?>"><?=htmlspecialchars($lot['title']); ?></a></h3>
            <div class="lot__state">
                <div class="lot__rate">
                    <?php if (intval($lot['rates_count']) === 0): ?>
                    <span class="lot__amount">Стартовая цена</span>
                    <span class="lot__cost"><?=format_price($lot['start_price']); ?></span>
                    <?php else: ?>
                    <span class="lot__amount"><?=$lot['rates_count']; ?> <?=set_format_phrase($lot['rates_count'], 'rate'); ?></span>
                    <span class="lot__cost"><?=format_price($lot['price']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="lot__timer timer">
                    <?=get_time_till_closing_time($lot['completion_date']); ?>
                </div>
            </div>
        </div>
    </li>
    <?php endforeach; ?>
    </ul>
    <?php endif; ?>
    </section>
    <?php if (!empty($pagination_information)): ?>
        <ul class="pagination-list">
            <?php foreach ($pagination_information as $info): ?>
            <li class="pagination-item<?=$info['class']; ?>">
                <a <?=$info['href']; ?>><?=$info['page_number']; ?></a>
            </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
