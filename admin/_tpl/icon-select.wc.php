<?php

    /**
     * Seletor de ícone do tema (flaticon.css), com fallback para navegadores sem appearance: base-select.
     *
     * Variáveis esperadas antes do require:
     * - $IconField: name/id do campo (ex.: svc_icon)
     * - $IconValue: classe atualmente salva (ex.: icon-mechanical)
     * - $IconLabel: rótulo acessível da lista (opcional)
     */
    $IconField ??= 'icon';
    $IconValue ??= '';
    $IconLabel ??= 'Ícone';

    // Lista de ícones extraída do flaticon.css do tema (classe => código do glyph)
    require __DIR__ . '/icon-list.wc.php';
?>
				<style>
					@font-face {
						font-family: 'wc-theme-icons';
						src: url('<?= $IconFonts; ?>.woff') format('woff'), url('<?= $IconFonts; ?>.ttf') format('truetype');
						font-display: block;
					}

					.wc_icon_select,
					.wc_icon_select::picker(select) {
						appearance: base-select;
					}

					.wc_icon_select {
						width: 100%;
						padding: 8px 12px;
						border: 1px solid #ccc;
						border-radius: 4px;
						background: #fff;
						font: inherit;
						color: #333;
						cursor: pointer;
					}

					.wc_icon_select:focus-visible {
						outline: 2px solid #00b494;
						outline-offset: 1px;
					}

					.wc_icon_select::picker(select) {
						max-height: 320px;
						padding: 4px;
						border: 1px solid #ccc;
						border-radius: 4px;
						box-shadow: 0 6px 18px rgba(0, 0, 0, .12);
					}

					.wc_icon_select::picker-icon {
						color: #888;
						transition: rotate .15s;
					}

					.wc_icon_select:open::picker-icon {
						rotate: 180deg;
					}

					.wc_icon_select option,
					.wc_icon_select selectedcontent {
						display: flex;
						align-items: center;
						gap: 10px;
						font-family: monospace;
					}

					.wc_icon_select option {
						padding: 6px 8px;
						border-radius: 3px;
					}

					.wc_icon_select option:hover,
					.wc_icon_select option:focus-visible {
						background: #f0f0f0;
					}

					.wc_icon_select option:checked {
						background: #e8f8f4;
						font-weight: bold;
					}

					.wc_icon_select option::checkmark {
						display: none;
					}

					.wc_icon_select .glyph {
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

					.wc_icon_select .glyph::before {
						content: attr(data-glyph);
					}

					/* Fallback (Firefox/Safari sem appearance: base-select) */
					.wc_icon_fb {
						position: relative;
						width: 100%;
					}

					.wc_icon_fb .glyph {
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

					.wc_icon_fb_toggle {
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

					.wc_icon_fb_toggle:focus-visible {
						outline: 2px solid #00b494;
						outline-offset: 1px;
					}

					.wc_icon_fb_toggle .name {
						flex: 1;
						font-family: monospace;
					}

					.wc_icon_fb_toggle .caret {
						font-size: 0.8em;
						color: #888;
						transition: transform .15s;
					}

					.wc_icon_fb.open .caret {
						transform: rotate(180deg);
					}

					.wc_icon_fb_list {
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

					.wc_icon_fb_list[hidden] {
						display: none;
					}

					.wc_icon_fb_list li {
						display: flex;
						align-items: center;
						gap: 10px;
						padding: 6px 8px;
						border-radius: 3px;
						font-family: monospace;
						color: #444;
						cursor: pointer;
					}

					.wc_icon_fb_list li:hover,
					.wc_icon_fb_list li.active {
						background: #f0f0f0;
					}

					.wc_icon_fb_list li[aria-selected="true"] {
						background: #e8f8f4;
						color: #000;
						font-weight: bold;
					}
				</style>
                <?php
                    $IconCurrent = isset($IconList[$IconValue]) ? $IconValue : '';
                ?>
				<div class='label'>
					<label class='label' for="<?= $IconField; ?>">
						<span class='legend'>ÍCONE</span>
						<select name="<?= $IconField; ?>" id="<?= $IconField; ?>" class="wc_icon_select">
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

						const select = document.getElementById('<?= $IconField; ?>');
						const label = select.closest('label');
						const root = document.createElement('div');
						const toggle = document.createElement('button');
						const list = document.createElement('ul');
						let active = null;
						let typed = '';
						let typedTimer = null;

						root.className = 'wc_icon_fb';
						toggle.type = 'button';
						toggle.className = 'wc_icon_fb_toggle';
						toggle.setAttribute('aria-haspopup', 'listbox');
						toggle.setAttribute('aria-expanded', 'false');
						toggle.innerHTML = '<i class="glyph" aria-hidden="true"></i><span class="name"></span><span class="caret">&#9660;</span>';
						list.className = 'wc_icon_fb_list';
						list.setAttribute('role', 'listbox');
						list.setAttribute('aria-label', '<?= $IconLabel; ?>');
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
