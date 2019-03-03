<section class="lot-item container">
    <h2><?=$lot['title']; ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
          <div class="lot-item__image">
            <img src="../<?=$lot['image']; ?>" width="730" height="548" alt="">
          </div>
          <p class="lot-item__category">Категория: <span><?=$lot['name']; ?></span></p>
          <p class="lot-item__description"><?=$lot['description']; ?></p>
        </div>
        <div class="lot-item__right">
          <?php if(!empty($user)): ?>
          <div class="lot-item__state">
            <div class="lot-item__timer timer">
                <?=get_time_till_closing_time($lot['completion_date']); ?>
            </div>
            <div class="lot-item__cost-state">
              <div class="lot-item__rate">
                <span class="lot-item__amount">Текущая цена</span>
                <span class="lot-item__cost"><?=format_price($lot['price']); ?></span>
              </div>
              <div class="lot-item__min-cost">
                Мин. ставка <span><?=format_price($lot['step_rate']); ?></span>
              </div>
            </div>
          </div>
          <?php endif;?>
        </div>
      </div>
</section>
