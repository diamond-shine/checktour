import breadcrumbs from '@plugins/breadcrumbs';
import { $t } from '@utils/i18n';

breadcrumbs.register('rostered.list', breadcrumb => {
    breadcrumb.push(
        $t('Rostered'),
        'rostered.list'
    );
});

export default breadcrumbs;