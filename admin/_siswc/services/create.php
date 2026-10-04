<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Helpers\Check;

    $AdminLevel = LEVEL_WC_SERVICES;
    if (!APP_SERVICES || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
    // AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();

    $ServiceDefaults = [
        'svc_id' => null,
        'svc_name' => '',
        'svc_title' => '',
        'svc_subtitle' => '',
        'svc_description' => '',
        'svc_cover' => '',
        'svc_icon' => '',
        'svc_category' => '',
        'svc_status' => 0,
    ];

    $SvcId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($SvcId) {
        $Read->exeRead(DB_SERVICES, 'WHERE svc_id = :id', 'id=' . $SvcId);
        if ($Read->getResult()) {
            $FormData = array_map(
                fn($v) => htmlspecialchars((string)(is_scalar($v) ? $v : ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                array_replace($ServiceDefaults, $Read->getResult()[0])
            );
            extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = sprintf(
                '<b>OPPSS %s</b>, você tentou editar um serviço que não existe ou que foi removido recentemente!',
                $Admin['user_name']
            );
            header('Location: dashboard.php?wc=services/home');

            exit;
        }
    } else {
        $Read->fullRead('SELECT count(svc_id) as Total FROM ' . DB_SERVICES . ' WHERE svc_status = :st', 'st=1');

        $SvcCreate = [
            'svc_created' => date('Y-m-d H:i:s'),
            'svc_status' => 0,
        ];
        $Create->exeCreate(DB_SERVICES, $SvcCreate);
        header('Location: dashboard.php?wc=services/create&id=' . $Create->getResult());

        exit;
    }

    $Search = filter_input_array(INPUT_POST);
    if ($Search && $Search['s']) {
        $S = urlencode((string)$Search['s']);
        header('Location: dashboard.php?wc=services/home&s=' . $S);

        exit;
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-hammer"><?php
                echo $svc_title ?? 'Novo Serviço'; ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=services/home">Serviços</a>
			<span class="crumb">/</span>
			Gerenciar Serviço
		</p>
	</div>

	<div class="dashboard_header_search">
		<a target="_blank" title="Ver no site" href="<?php
            echo BASE . ('/servico/' . $svc_name); ?>"
		   class="wc_view btn btn_green icon-eye">Ver no Site!</a>
	</div>
</header>

<div class="workcontrol_imageupload none" id="post_control">
	<div class="workcontrol_imageupload_content">
		<form name="workcontrol_post_upload" action="" method="post" enctype="multipart/form-data">
			<input type="hidden" name="callback" value="Services"/>
			<input type="hidden" name="callback_action" value="sendimage"/>
			<input type="hidden" name="svc_id" value="<?php
                echo $SvcId; ?>"/>
			<div class="upload_progress none"
			     style="padding: 5px; background: #00B594; color: #fff; width: 0%; text-align: center; max-width: 100%;">
				0%
			</div>
			<div style="overflow: auto; max-height: 300px;">
				<img class="image image_default" alt="Nova Imagem" title="Nova Imagem"
				     src="../tim.php?src=admin/_img/no_image.jpg&w=<?php
                         echo IMAGE_W; ?>&h=<?php
                         echo IMAGE_H; ?>"
				     default="../tim.php?src=admin/_img/no_image.jpg&w=<?php
                         echo IMAGE_W; ?>&h=<?php
                         echo IMAGE_H; ?>"/>
			</div>
			<div class="workcontrol_imageupload_actions">
				<input class="wc_loadimage" type="file" name="image" required/>
				<span class="workcontrol_imageupload_close icon-cancel-circle btn btn_red" id="post_control"
				      style="margin-right: 8px;">Fechar</span>
				<button class="btn btn_green icon-image">Enviar e Inserir!</button>
				<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
				     title="Enviando Requisição!" src="_img/load.gif"/>
			</div>
			<div class="clear"></div>
		</form>
	</div>
</div>

<div class="dashboard_content single_svc_form">
	<form class="auto_save" name="manage_svc" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="callback" value="Services"/>
		<input type="hidden" name="callback_action" value="manager"/>
		<input type="hidden" name="svc_id" value="<?php
            echo $SvcId; ?>"/>

		<div class="box box70">
			<div class="box_content">
				<label class="label">
					<span class="legend">Serviço:</span>
					<input class="font_large" type="text" name="svc_title" value="<?php
                        echo $svc_title; ?>"
					       placeholder="Nome do Serviço:" required/>
				</label>

				<label class="label">
					<span class="legend">Breve Descrição:</span>
					<textarea class="font_medium" name="svc_subtitle" rows="3"
					          required><?php
                            echo $svc_subtitle; ?></textarea>
				</label>

				<label class="label">
					<span class="legend">Descrição Completa:</span>
					<textarea name="svc_description" class="work_mce" rows="10"><?php
                            echo $svc_description; ?></textarea>
				</label>

				<label class="label">
					<span class="legend">Categoria:</span>
					<select name="svc_category">
						<option value="">Selecione uma categoria</option>
                        <?php
                            $Read->exeRead(
                                DB_SERVICES_CATEGORIES,
                                'WHERE category_parent IS NULL ORDER BY category_title ASC'
                            );
                            if ($Read->getResult()) {
                                foreach ($Read->getResult() as $Category) {
                                    $Selected = ((string)$svc_category === (string)$Category['category_id'] ? ' selected' : '');
                                    echo "<option value='{$Category['category_id']}'{$Selected}>{$Category['category_title']}</option>";

                                    $Read->exeRead(
                                        DB_SERVICES_CATEGORIES,
                                        'WHERE category_parent = :parent ORDER BY category_title ASC',
                                        'parent=' . $Category['category_id']
                                    );
                                    if ($Read->getResult()) {
                                        foreach ($Read->getResult() as $SubCategory) {
                                            $Selected = ((string)$svc_category === (string)$SubCategory['category_id'] ? ' selected' : '');
                                            echo "<option value='{$SubCategory['category_id']}'{$Selected}>&raquo;&raquo; {$SubCategory['category_title']}</option>";
                                        }
                                    }
                                }
                            }
                        ?>
					</select>
				</label>

				<div class="clear"></div>
			</div>
		</div>

		<div class="box box30">
			<div class="panel_header default">
				<h2 class="icon-file-picture">Imagem Principal do Serviço:</h2>
				<label class='label'>
					<span class='legend'>Tamanho (WEBP <?= '410X300' ?>px):</span>
					<input type="file" class="wc_loadimage" name="svc_cover"/>
				</label>
                <?php
                    $Image = (file_exists('../uploads/' . $svc_cover) && !is_dir(
                        '../uploads/' . $svc_cover
                    ) ? 'uploads/' . $svc_cover : 'admin/_img/no_image.jpg');
                ?>
				<img class="svc_cover" alt="Capa do Serviço" title="Capa do Serviço"
				     src="../tim.php?src=<?php
                         echo $Image; ?>&w=410&h=300"
				     default="../tim.php?src=<?php
                         echo $Image; ?>&w=410&h=300">
                <?php
                    $Read->exeRead(DB_SERVICES_GALLERY, 'WHERE svc_id = :id', 'id=' . $svc_id);
                    if ($Read->getResult()) {
                        echo '<div class="pdt_images gallery pdt_single_image">';
                        foreach ($Read->getResult() as $Image) {
                            $ImageUrl = ($Image['image'] && file_exists(
                                '../uploads/' . $Image['image']
                            ) && !is_dir(
                                '../uploads/' . $Image['image']
                            ) ? '../uploads/' . $Image['image'] : '_img/no_image.jpg');
                            echo sprintf(
                                "<img rel='Services' id='%s' alt='Imagem em %s' title='Imagem em %s' src='%s'/>",
                                $Image['id'],
                                $svc_title,
                                $svc_title,
                                $ImageUrl
                            );
                        }
                        echo '</div>';
                    } else {
                        echo '<div class="pdt_images gallery pdt_single_image"></div>';
                    }
                ?>
			</div>

			<div class=" box_content">
				<label class="label">
					<span class="legend">Fotos Adicionais (JPG <?php
                            echo IMAGE_W; ?>x<?php
                            echo IMAGE_H; ?>px):</span>
					<input type="file" name="image[]" multiple/>
				</label>

                <?php
                    // Lista de ícones extraída do flaticon.css do tema (classe => código do glyph)
                    $IconList = [];
                    $IconCss = __DIR__ . '/../../../themes/' . THEME . '/assets/css/flaticon.css';
                    if (is_file($IconCss) && preg_match_all(
                        '/\.(icon-[\w-]+):before\s*\{\s*content:\s*"\\\\([0-9a-f]+)"/i',
                        (string) file_get_contents($IconCss),
                        $IconMatches,
                        PREG_SET_ORDER
                    )) {
                        foreach ($IconMatches as $IconMatch) {
                            $IconList[$IconMatch[1]] = $IconMatch[2];
                        }
                        ksort($IconList);
                    }
                    $IconFonts = BASE . '/themes/' . THEME . '/assets/fonts/icomoon';
                ?>
				<style>
					@font-face {
						font-family: 'wc-theme-icons';
						src: url('<?= $IconFonts; ?>.woff') format('woff'), url('<?= $IconFonts; ?>.ttf') format('truetype');
						font-display: block;
					}

					.svc_icon_select,
					.svc_icon_select::picker(select) {
						appearance: base-select;
					}

					.svc_icon_select {
						width: 100%;
						padding: 8px 12px;
						border: 1px solid #ccc;
						border-radius: 4px;
						background: #fff;
						font: inherit;
						color: #333;
						cursor: pointer;
					}

					.svc_icon_select:focus-visible {
						outline: 2px solid #00b494;
						outline-offset: 1px;
					}

					.svc_icon_select::picker(select) {
						max-height: 320px;
						padding: 4px;
						border: 1px solid #ccc;
						border-radius: 4px;
						box-shadow: 0 6px 18px rgba(0, 0, 0, .12);
					}

					.svc_icon_select::picker-icon {
						color: #888;
						transition: rotate .15s;
					}

					.svc_icon_select:open::picker-icon {
						rotate: 180deg;
					}

					.svc_icon_select option,
					.svc_icon_select selectedcontent {
						display: flex;
						align-items: center;
						gap: 10px;
						font-family: monospace;
					}

					.svc_icon_select option {
						padding: 6px 8px;
						border-radius: 3px;
					}

					.svc_icon_select option:hover,
					.svc_icon_select option:focus-visible {
						background: #f0f0f0;
					}

					.svc_icon_select option:checked {
						background: #e8f8f4;
						font-weight: bold;
					}

					.svc_icon_select option::checkmark {
						display: none;
					}

					.svc_icon_select .glyph {
						display: inline-flex;
						justify-content: center;
						width: 32px;
						font-family: 'wc-theme-icons';
						font-style: normal;
						font-weight: normal;
						font-size: 24px;
						line-height: 1;
						flex-shrink: 0;
					}

					.svc_icon_select .glyph::before {
						content: attr(data-glyph);
					}

					/* Fallback (Firefox/Safari sem appearance: base-select) */
					.svc_icon_fb {
						position: relative;
						width: 100%;
					}

					.svc_icon_fb .glyph {
						display: inline-flex;
						justify-content: center;
						width: 32px;
						font-family: 'wc-theme-icons';
						font-style: normal;
						font-weight: normal;
						font-size: 24px;
						line-height: 1;
						color: #333;
						flex-shrink: 0;
					}

					.svc_icon_fb_toggle {
						display: flex;
						align-items: center;
						gap: 10px;
						width: 100%;
						padding: 8px 12px;
						border: 1px solid #ccc;
						border-radius: 4px;
						background: #fff;
						font: inherit;
						color: #333;
						text-align: left;
						cursor: pointer;
					}

					.svc_icon_fb_toggle:focus-visible {
						outline: 2px solid #00b494;
						outline-offset: 1px;
					}

					.svc_icon_fb_toggle .name {
						flex: 1;
						font-family: monospace;
					}

					.svc_icon_fb_toggle .caret {
						font-size: 0.8em;
						color: #888;
						transition: transform .15s;
					}

					.svc_icon_fb.open .caret {
						transform: rotate(180deg);
					}

					.svc_icon_fb_list {
						position: absolute;
						top: calc(100% + 4px);
						left: 0;
						right: 0;
						z-index: 50;
						max-height: 320px;
						overflow-y: auto;
						margin: 0;
						padding: 4px;
						list-style: none;
						border: 1px solid #ccc;
						border-radius: 4px;
						background: #fff;
						box-shadow: 0 6px 18px rgba(0, 0, 0, .12);
					}

					.svc_icon_fb_list[hidden] {
						display: none;
					}

					.svc_icon_fb_list li {
						display: flex;
						align-items: center;
						gap: 10px;
						padding: 6px 8px;
						border-radius: 3px;
						font-family: monospace;
						color: #444;
						cursor: pointer;
					}

					.svc_icon_fb_list li:hover,
					.svc_icon_fb_list li.active {
						background: #f0f0f0;
					}

					.svc_icon_fb_list li[aria-selected="true"] {
						background: #e8f8f4;
						color: #000;
						font-weight: bold;
					}
				</style>
                <?php
                    $IconCurrent = isset($IconList[$svc_icon]) ? $svc_icon : '';
                ?>
				<div class='label'>
					<label class='label' for="svc_icon">
						<span class='legend'>ÍCONE</span>
						<select name="svc_icon" id="svc_icon" class="svc_icon_select">
							<button>
								<selectedcontent></selectedcontent>
							</button>
							<option value=""<?= $IconCurrent ? '' : ' selected'; ?>>
								<i class="glyph" aria-hidden="true"></i>
								<span>Selecione um ícone</span>
							</option>
                            <?php
                                foreach ($IconList as $IconClass => $IconCode) {
                                    printf(
                                        '<option value="%1$s" data-glyph="&#x%2$s;"%3$s><i class="glyph" data-glyph="&#x%2$s;" aria-hidden="true"></i><span>%1$s</span></option>',
                                        $IconClass,
                                        $IconCode,
                                        $IconClass === $IconCurrent ? ' selected' : ''
                                    );
                                }
                            ?>
						</select>
					</label>
				</div>
				<script>
					(function () {
						if (window.CSS && CSS.supports('appearance', 'base-select')) {
							return;
						}

						const select = document.getElementById('svc_icon');
						const label = select.closest('label');
						const root = document.createElement('div');
						const toggle = document.createElement('button');
						const list = document.createElement('ul');
						let active = null;
						let typed = '';
						let typedTimer = null;

						root.className = 'svc_icon_fb';
						toggle.type = 'button';
						toggle.className = 'svc_icon_fb_toggle';
						toggle.setAttribute('aria-haspopup', 'listbox');
						toggle.setAttribute('aria-expanded', 'false');
						toggle.innerHTML = '<i class="glyph" aria-hidden="true"></i><span class="name"></span><span class="caret">&#9660;</span>';
						list.className = 'svc_icon_fb_list';
						list.setAttribute('role', 'listbox');
						list.setAttribute('aria-label', 'Ícone do serviço');
						list.tabIndex = -1;
						list.hidden = true;

						const items = Array.from(select.options).map((option) => {
							const li = document.createElement('li');
							const glyph = document.createElement('i');
							const name = document.createElement('span');
							li.setAttribute('role', 'option');
							li.dataset.value = option.value;
							glyph.className = 'glyph';
							glyph.setAttribute('aria-hidden', 'true');
							glyph.textContent = option.dataset.glyph || '';
							name.textContent = option.value || 'Selecione um ícone';
							li.append(glyph, name);
							list.appendChild(li);
							return li;
						});

						function render() {
							const option = select.options[select.selectedIndex];
							toggle.querySelector('.glyph').textContent = option.dataset.glyph || '';
							toggle.querySelector('.name').textContent = option.value || 'Selecione um ícone';
							items.forEach((li, i) => li.setAttribute('aria-selected', i === select.selectedIndex ? 'true' : 'false'));
						}

						function setActive(li) {
							items.forEach((o) => o.classList.remove('active'));
							active = li;
							if (li) {
								li.classList.add('active');
								const top = li.offsetTop;
								if (top < list.scrollTop || top + li.offsetHeight > list.scrollTop + list.clientHeight) {
									list.scrollTop = top - list.clientHeight / 2;
								}
							}
						}

						function open() {
							list.hidden = false;
							root.classList.add('open');
							toggle.setAttribute('aria-expanded', 'true');
							setActive(items[select.selectedIndex]);
							list.focus();
						}

						function close(focusToggle) {
							list.hidden = true;
							root.classList.remove('open');
							toggle.setAttribute('aria-expanded', 'false');
							if (focusToggle) {
								toggle.focus();
							}
						}

						function choose(li) {
							select.value = li.dataset.value;
							select.dispatchEvent(new Event('change', {bubbles: true}));
							render();
							close(true);
						}

						toggle.addEventListener('click', () => list.hidden ? open() : close(false));

						toggle.addEventListener('keydown', (e) => {
							if (['ArrowDown', 'ArrowUp', 'Enter', ' '].includes(e.key)) {
								e.preventDefault();
								open();
							}
						});

						list.addEventListener('click', (e) => {
							const li = e.target.closest('li');
							if (li) {
								choose(li);
							}
						});

						list.addEventListener('keydown', (e) => {
							const index = items.indexOf(active);
							if (e.key === 'ArrowDown') {
								e.preventDefault();
								setActive(items[Math.min(index + 1, items.length - 1)]);
							} else if (e.key === 'ArrowUp') {
								e.preventDefault();
								setActive(items[Math.max(index - 1, 0)]);
							} else if (e.key === 'Enter' || e.key === ' ') {
								e.preventDefault();
								if (active) {
									choose(active);
								}
							} else if (e.key === 'Escape' || e.key === 'Tab') {
								close(e.key === 'Escape');
							} else if (e.key.length === 1) {
								// Busca por digitação, como no select nativo
								clearTimeout(typedTimer);
								typed += e.key.toLowerCase();
								typedTimer = setTimeout(() => typed = '', 600);
								const match = items.find((li) => li.dataset.value.replace(/^icon-/, '').startsWith(typed));
								if (match) {
									setActive(match);
								}
							}
						});

						document.addEventListener('click', (e) => {
							if (!root.contains(e.target)) {
								close(false);
							}
						});

						select.addEventListener('change', render);
						select.style.display = 'none';
						select.tabIndex = -1;
						label.removeAttribute('for');
						root.append(toggle, list);
						label.after(root);
						render();
					})();
				</script>

				<div class="m_top">&nbsp;</div>

				<div class="wc_actions">
					<div class='switch'>
						<input name='svc_status' type='checkbox' id='svc_status'
						       value='1' <?php
                            echo 1 == $svc_status ? 'checked' : ''; ?>>
						<label for="svc_status" data-on="ON" data-off="OFF"></label>
					</div>

					<button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
					<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
					     title="Enviando Requisição!" src="_img/load.gif"/>
				</div>
				<div class="clear"></div>
                <?php
                    $URLSHARE = '/servico/' . $svc_name;
                    $pdt_title = $svc_title;
                    $pdt_subtitle = $svc_subtitle;

                    require __DIR__ . '/../../_tpl/share.wc.php';
                ?>
			</div>
		</div>
		<div class="clear"></div>
	</form>
</div>
