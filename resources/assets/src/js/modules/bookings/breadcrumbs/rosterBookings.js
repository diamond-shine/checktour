import breadcrumbs from '@plugins/breadcrumbs';
import '@modules/rosters/breadcrumbs/';
import { $t } from '@utils/i18n';

breadcrumbs.register('bookings.roster-bookings', (breadcrumb, model) => {
    breadcrumb.parent('rosters.view', model);
    breadcrumb.push(
        $t('Bookings'),
        {
            name: 'bookings.roster-bookings',
            params: {
                rosterId: model.id
            }
        }
    );
});


export default breadcrumbs;