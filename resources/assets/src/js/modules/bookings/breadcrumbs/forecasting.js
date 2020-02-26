import breadcrumbs from '@plugins/breadcrumbs';
import { $t } from '@utils/i18n';

breadcrumbs.register('forecasting.list', breadcrumb => {
    breadcrumb.push(
        $t('Forecasting'),
        'forecasting.list'
    );
});

export default breadcrumbs;