/**
 * 指定されたロール名に対応する表示名を取得する。
 * ロール名が 'admin', 'manager', 'user' のいずれかの場合、
 * それぞれ '管理者', '店舗代表者', '利用者' という表示名を返す。
 * なお指定されたロール名に対応する表示名が存在しない場合は、ロール名自体を返す。
 *
 * @param {string} roleName - 取得するロール名
 * @returns {string} 対応する表示名、またはロール名
 */
export const getRoleDisplayName = (roleName) => {
    const roleDisplayNames = {
        'admin': '管理者',
        'manager': '店舗代表者',
        'user': '利用者',
    };
    return roleDisplayNames[roleName] || roleName;
};
