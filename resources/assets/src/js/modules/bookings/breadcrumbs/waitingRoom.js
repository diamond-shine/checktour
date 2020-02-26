import breadcrumbs from '@plugins/breadcrumbs';
import { $t } from '@utils/i18n';

breadcrumbs.register('waiting-room.list', breadcrumb => {
    breadcrumb.push(
        $t('Waiting room'),
        'waiting-room.list'
    );
});

export default breadcrumbs;