<form class="form container<?=empty($errors) && !$is_error_authentication ? '' : ' form--invalid'; ?>" action="login.php" method="post">
    <h2>Вход</h2>
    <div class="form__item<?=isset($errors['email']) ? ' form__item--invalid' : ''; ?>">
        <label for="email">E-mail*</label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" required<?=empty($information['email']) ? '' : ' value="' . $information['email'] . '"'; ?>>
        <span class="form__error"><?=isset($errors['email']) ? $errors['email'] : ''; ?></span>
    </div>
    <div class="form__item form__item--last<?=isset($errors['password']) ? ' form__item--invalid' : ''; ?>">
        <label for="password">Пароль*</label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" required>
        <span class="form__error"><?=isset($errors['password']) ? $errors['password'] : ''; ?></span>
    </div>
    <span class="form__error form__error--bottom">Вы ввели неверный e-mail / пароль.</span>
    <button type="submit" class="button">Войти</button>
</form>
