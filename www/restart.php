                <!-- h2 stays for breadcrumbs -->
                <h2><a href="#">Администрация</a> &raquo; <a href="#" class="active">Сървър рестарт</a></h2>
                <div id="main">
				<fieldset>
					<form action="/submit/res.php" method="POST">
					<label for="res"><b><img src="/images/icons/resicon.png" />Видове рестарт:</b></label>
					<p>
					<select name="res">
					<option value="sv">Рестарт на рунда</option>
					<option value="servres">Рестарт на сървъра</option>
					<option value="sysres">Системен рестарт</option>
					</select>
					</p>
					<p>
					<input type="submit" value="Рестарт" />
					</p>
					</form>
					<br />
					<b>sv_restart</b> - Рестартирва рунда след 3 секунди <br />
					<b>Сървър рестарт</b> -  Рестартирва сървъра чрез командата <i>restart</i> <br />
					<b>Системен рестарт</b> - Спира сървъра системно след което го активира без да губите информация (нужно е играчите да се закачат отново към сървъра)
				</fieldset>
				</div>