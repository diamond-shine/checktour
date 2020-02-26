import breadcrumbs from '@plugins/breadcrumbs';
import { $t } from '@utils/i18n';

breadcrumbs.register('processed.list', breadcrumb => {
    breadcrumb.push(
        $t('Processed'),
        'processed.list'
    );
});

export default breadcrumbs;