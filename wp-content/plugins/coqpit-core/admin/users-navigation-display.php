<?php
$navigations = \COQPIT\Plugins\Core\WordPress\Access::getAdminNavigations();
?>
<h2><?php _e('Gestion de l\'accès aux menus pour le rôle "Gestionnaire"', 'coqpit-core'); ?></h2>
<p><?php _e('Contrôlez l\'affichage et l\'accès aux menus dans le back-office pour vos clients à qui vous définirez le rôle "Gestionnaire", cela vous permettra de leur offrir une interface simplifiée.', 'coqpit-core'); ?></p>
<div class="displayed-menus">
    <ul class="parents">
        <?php foreach ($navigations as $parentSlug => $item) : ?>
            <li class="parent">
                <div class="parent-settings">
                    <div class="name">
                        <?php if(count($item['children'])): ?>
                            <span class="thumb-arrow"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M16.293 9.293 12 13.586 7.707 9.293l-1.414 1.414L12 16.414l5.707-5.707z"></path></svg></span>
                        <?php endif; ?>
                        <?php echo $item['name']; ?>
                    </div>
                    <div class="actions">
                        <label class="toggle-checkbox<?php echo ($item['locked']) ? ' is-locked' : ''; ?>">
                            <input type="checkbox" name="coqpit_access_menus[]" value="<?php echo $parentSlug; ?>"<?php echo (!$item['display']) ? ' checked="checked"': ''; ?>>
                            <span class="is-true"><?php _e('Autoriser', 'coqpit-core'); ?></span>
                            <span class="is-false"><?php _e('Refuser', 'coqpit-core'); ?></span>
                        </label>
                    </div>
                </div>
                <?php if(count($item['children'])) : ?>
                    <div class="children-wrapper">
                        <ul class="children">
                            <?php foreach ($item['children'] as $childSlug => $child) : ?>
                                <li class="child">
                                    <div class="child-settings">
                                        <div class="name"><?php echo $child['name']; ?></div>
                                        <div class="actions">
                                            <label class="toggle-checkbox<?php echo ($child['locked'] || (!$item['display'] && $item['locked'])) ? ' is-locked' : ''; ?>">
                                                <input type="checkbox" name="coqpit_access_submenus[]" value="<?php echo $childSlug; ?>"<?php echo (!$child['display']) ? ' checked="checked"': ''; ?>>
                                                <span class="is-true"><?php _e('Autoriser', 'coqpit-core'); ?></span>
                                                <span class="is-false"><?php _e('Refuser', 'coqpit-core'); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
