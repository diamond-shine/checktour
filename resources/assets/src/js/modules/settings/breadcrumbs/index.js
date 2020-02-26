import breadcrumbs from '@plugins/breadcrumbs';
import { $t } from '@utils/i18n';

breadcrumbs.register('settings.edit', breadcrumb => {
    breadcrumb.push($t('Settings'), 'settings.edit');
});

export default breadcrumbs;
