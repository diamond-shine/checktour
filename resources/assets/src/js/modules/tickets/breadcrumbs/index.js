import breadcrumbs from '@plugins/breadcrumbs';
import '@modules/tours/breadcrumbs';
import { $t } from '@utils/i18n';

breadcrumbs.register('tickets.list', breadcrumb => {
    breadcrumb.push(
        $t('Tickets'),
        'tickets.list'
    );
});


breadcrumbs.register('tickets.list', (breadcrumb, model) => {
    breadcrumb.parent('tours.view', model);
    breadcrumb.push(
        $t('Tickets'),
        'tickets.list'
    );
});

export default breadcrumbs;