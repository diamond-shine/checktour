const hasPermission = function (args) {
    return require('@store').default.getters[ 'guard/can' ](args);
};

const hasAnyPermission = function (args) {
    return require('@store').default.getters[ 'guard/canAny' ](args);
};

const hasAllPermissions = function (permission) {
    return true;
};

const hasRole = function (role) {
    return true;
};

const hasAnyRole = function (role) {
    return true;
};

const hasAllRoles = function (role) {
    return true;
};

export default {
    hasPermission,
    hasAnyPermission,
    hasAllPermissions,
    hasRole,
    hasAnyRole,
    hasAllRoles
};
