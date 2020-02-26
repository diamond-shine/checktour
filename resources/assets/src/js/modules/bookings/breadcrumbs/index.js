import breadcrumbs from '@plugins/breadcrumbs';
import { $t } from '@utils/i18n';

breadcrumbs.register('bookings.list', breadcrumb => {
    breadcrumb.push(
        $t('All bookings'),
        'bookings.list'
    );
});

export default breadcrumbs;