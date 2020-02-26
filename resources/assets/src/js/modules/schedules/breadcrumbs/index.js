import breadcrumbs from '@plugins/breadcrumbs';
import { $t } from '@utils/i18n';

breadcrumbs.register('schedules.list', (breadcrumb) => {
    breadcrumb.push(
        $t('Sessions'),
        'schedules.list'
    );
});

export default breadcrumbs;
