<?php  $user_can_register = get_option( 'users_can_register' );?>

<div class="modal fade" id="wpalf-login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" align="center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
                        <span class="glyphicon glyphicon-remove" aria-hidden="true">x</span>
                    </button>
                    <!-- <img class="img-circle" id="img_logo" src="../images/bootsnip-logo.jpg"> -->
                </div>
                
                
                <div id="wpalf-div-forms">
                    <p class="status"></p>
                    
                    <form id="wpalf-login-form" method="post">
                        <div class="modal-body">
                            <div id="div-login-msg">
                                <div id="icon-login-msg" class="glyphicon glyphicon-chevron-right"></div>
                                <span id="text-login-msg">Введите логин/e-mail и пароль</span>
                            </div>
                            <input id="wpalf-username" class="form-control" type="text" placeholder="Логин или e-mail" name="username" required>
                            <input id="wpalf-password" class="form-control" type="password" placeholder="Пароль" name="password" required>
                            <!-- <div class="g-recaptcha" data-sitekey="<?php echo get_option('captcha_site_key'); ?>"></div> -->
                            <div class="clear"></div>
                            <div id="g-recaptcha-login"></div>
                        </div>
                        <div class="modal-footer">
                            <div>
                                <button type="submit" class="btn btn-primary btn-lg btn-block">Войти</button>
                            </div>
                            <div>
                                <a href="#" id="login_lost_btn" type="button" class="btn btn-link">Забыли пароль?</a>
                                <?php if ($user_can_register) { ?> 
                                <a href="#" id="login_register_btn" type="button" class="btn btn-link">Зарегистрироваться</a>
                                <?php } ?>
                            </div>
                        </div>
                        <?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
                    </form>
                    
                    
                    
                    <form id="wpalf-lost-form" style="display:none;">
                        <div class="modal-body">
                            <div id="div-lost-msg">
                                <div id="icon-lost-msg" class="glyphicon glyphicon-chevron-right"></div>
                                <span id="text-lost-msg">Введите ваш e-mail.</span>
                            </div>
                            <input id="wpalf-lost_email" class="form-control" type="text" placeholder="E-Mail" required>
                            <div class="clear"></div>
                            <div id="g-recaptcha-lost"></div>
                        </div>
                        <div class="modal-footer">
                            <div>
                                <button type="submit" class="btn btn-primary btn-lg btn-block">Отправить</button>
                            </div>
                            <div>
                                <a href="#" id="lost_login_btn" type="button" class="btn btn-link">Войти</a>
                                <?php if ($user_can_register) { ?> 
                                <a href="#" id="lost_register_btn" type="button" class="btn btn-link">Зарегистрироваться</a>
                                <?php } ?>
                            </div>
                        </div>
                    </form>
                    
                    
                    <?php if ($user_can_register) { ?> 
                    <form id="wpalf-register-form" style="display:none;">
                        <div class="modal-body">
                            <div id="div-register-msg">
                                <div id="icon-register-msg" class="glyphicon glyphicon-chevron-right"></div>
                                <span id="text-register-msg">Регистрация аккаунта</span>
                            </div>
                            <input id="register_username" class="form-control" type="text" placeholder="Логин" required>
                            <input id="register_email" class="form-control" type="text" placeholder="E-Mail" required>
                            <!-- <input id="register_password" class="form-control" type="password" placeholder="Пароль" required> -->
                            <div class="clear"></div>
                            <div id="g-recaptcha-reg"></div>
                        </div>
                        <div class="modal-footer">
                            <div>
                                <button type="submit" class="btn btn-primary btn-lg btn-block">Зарегистрироваться</button>
                            </div>
                            <div>
                                <a href="#" id="register_login_btn" type="button" class="btn btn-link">Войти</a>
                                <a href="#" id="register_lost_btn" type="button" class="btn btn-link">Забыли пароль?</a>
                            </div>
                        </div>
                    </form>
                    <?php } ?>
                    
                    
                </div>
                
                
            </div>
        </div>
</div>
<!-- <button id="wpalf-btn">Login</button> -->