<?php
$name = $_POST['lot-name'] ?? '';
$message = $_POST['message'] ?? '';
$image = $_POST['lot_img'] ?? '';
$start_rate = $_POST['lot-rate'] ?? '';
$step = $_POST['lot-step'] ?? '';
$date = $_POST['lot-date'] ?? '';
?>
<form class="form form--add-lot container <?=($errors) ? "form--invalid" : "";?>" enctype="multipart/form-data" action="../add.php" method="post">
    <h2>Добавление лота</h2>
    <div class="form__container-two">
        <div class="form__item <?=!isset($errors['lot-name']) ? '' : ' form__item--invalid'; ?>">
          <label for="lot-name">Наименование</label>
          <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота" value="<?=$name; ?>" required>
          <?php if (isset($errors['lot-name'])): ?>
          <span class="form__error">Введите наименование лота</span>
          <?php endif; ?>
        </div>
        <div class="form__item<?=!isset($errors['category']) ? '' : ' form__item--invalid'; ?>">
            <label for="category">Категория</label>
            <select id="category" name="category" required>
                <option value="" selected disabled>Выберите категорию</option>
                <?php foreach ($categories as $category): ?>
                <option value="<?=$category['category_id']; ?>"<?=(!empty($lot['category']) && $lot['category'] === $category['category_id']) ? ' selected' : ''; ?>><?=$category['name']; ?></option>
                <?php endforeach; ?>
            </select>
          <?php if (isset($errors['category'])): ?>
          <span class="form__error">Выберите категорию</span>
          <?php endif; ?>
        </div>
    </div>
    <div class="form__item form__item--wide <?=!isset($errors['message']) ? '' : ' form__item--invalid'; ?>">
        <label for="message">Описание</label>
        <textarea id="message" name="message" placeholder="Напишите описание лота" required><?=$message;?></textarea>
        <?php if (isset($errors['message'])): ?>
        <span class="form__error">Напишите описание лота</span>
        <?php endif; ?>

    </div>

    <?php $class = isset($errors['lot_img']) ? "form__item--invalid" : ""; ?>
      <?php if ($image): ?>
      <div class="form__item form__item--file form__item--uploaded <?=$class;?>">
      <?php else: ?>
      <div class="form__item form__item--file <?=$class;?>">
    <?php endif; ?>

    <label>Изображение</label>
        <div class="preview">
          <button class="preview__remove" type="button">x</button>
          <div class="preview__img">
            <img src="img/avatar.jpg" width="113" height="113" alt="Изображение лота">
          </div>
        </div>
        <div class="form__input-file">
          <input class="visually-hidden" name="lot_img" type="file" id="photo2" value="">
          <label for="photo2">
            <span>+ Добавить</span>
          </label>
          <?php if (isset($errors['lot_img'])):?>
            <span class="form__error"><?=$errors['lot_img'];?></span>
          <?php endif; ?>
        </div>
    </div>
    <div class="form__container-three">
        <div class="form__item form__item--small <?=!isset($errors['lot-rate']) ? '' : ' form__item--invalid'; ?>">
          <label for="lot-rate">Начальная цена</label>
          <input id="lot-rate" type="number" name="lot-rate" placeholder="0" value="<?=$start_rate;?>"required>
          <?php if (isset($errors['lot-rate'])):?>
          <span class="form__error">Введите начальную цену</span>
          <?php endif; ?>
        </div>
        <div class="form__item form__item--small <?=!isset($errors['lot-step']) ? '' : ' form__item--invalid'; ?>">
          <label for="lot-step">Шаг ставки</label>
          <input id="lot-step" type="number" name="lot-step" placeholder="0" value="<?=$step;?>" required>
          <?php if (isset($errors['lot-step'])):?>
          <span class="form__error">Введите шаг ставки</span>
          <?php endif; ?>
        </div>
        <div class="form__item <?=!isset($errors['lot-date']) ? '' : ' form__item--invalid'; ?>">
          <label for="lot-date">Дата окончания торгов</label>
          <input class="form__input-date" id="lot-date" type="date" name="lot-date" value="<?=$date;?>" required>
          <?php if (isset($errors['lot-date'])):?>
          <span class="form__error">Введите дату завершения торгов</span>
          <?php endif; ?>
        </div>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Добавить лот</button>
</form>
