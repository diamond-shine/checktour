import breadcrumbs from '@plugins/breadcrumbs';
import '@modules/tours/breadcrumbs';
import { $t } from '@utils/i18n';

breadcrumbs.register('tour-options.list', (breadcrumb, model) => {
    breadcrumb.parent('tours.view', model);
    breadcrumb.push(
        $t('Tour options'),
        'tour-options.list'
    );
});

export default breadcrumbs;