		<footer>
			<?php if (!empty($_SESSION['id'])) : ?>
				<div id="logout">
					<form action="" method="post">
						<div class="logout-btn--parts"><input type="submit" name="logout" value="ログアウト" class="btn logout-btn"></div>
					</form>
				</div>

			<?php else : ?>
				<div class="authorization-area">
					<a class="authorization-btn" href="login.php">
						<p class="authorization-text">ログイン</p>
					</a>
					<a class="authorization-btn" href="pre_registration.php">
						<p class="authorization-text">会員登録</p>
					</a>
				</div>
			<?php endif; ?>
		</footer>