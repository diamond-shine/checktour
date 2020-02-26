// import factory from '@utils/store-factories';
import { list, view, update} from '@utils/store-factories';
import { taskManager } from '@plugins/taskManager';
// import http from '@utils/http';
import filters from '@utils/filters';


const MODULES = 'rosters';

export default {
    namespaced: true,
    actions: {
        list: list(MODULES),

        view: view(MODULES),

        update: update(MODULES)
    }
};