/**
 * @requires computed - リアクティブなプロパティの計算やウォッチャーを作成するために使用
 * @requires ref - リアクティブなデータ参照を作成するために使用
 * @requires YubinBangoCore - yubinbango-core2ライブラリからインポート、日本の郵便番号から住所情報を取得する機能を提供
 */
import { computed, ref } from "vue";
import { Core as YubinBangoCore } from "yubinbango-core2";

/**
 * 店舗登録・編集画面でのフォームを扱うためのリアクティブなプロパティとメソッドを提供する。
 *
 * @param {Object} props - コンポーネントのプロパティ
 * @param {Object} form - リアクティブなフォームオブジェクト
 * @returns {Object} - リアクティブなプロパティとメソッドを含むオブジェクト
 */
export function useRestaurantForm(props, form) {
    /**
     * 選択されたジャンルの名前を取得する算出プロパティ。
     *
     * @returns {string} - 選択されたジャンルの名前、または選択されていない場合は「未選択」を返す
     */
    const selectedGenreName = computed(() => {
        const selectedGenre = props.genres.find((genre) => genre.id === form.genre_id);
        return selectedGenre ? selectedGenre.name : "未選択";
    });

    /**
     * 電話番号入力を検証し、フォーマットをおこなう。
     * 全角文字などを削除し、数字とハイフンのみを許可する。
     */
    const validatePhoneNumber = () => {
        form.tel = form.tel
            // ASCIIコード範囲外の文字を削除する（例：全角、絵文字や特殊文字の除去）
            .replace(/[^\x20-\x7E]/g, "")
            // 数字とハイフン以外の文字を削除する
            .replace(/[^0-9\-]/g, "");
    };

    /**
     * 郵便番号入力を検証し、フォーマットをおこなう。
     * 正規表現を使用して非数字を除外し、最大7桁に制限する。
     */
    const validatePostal = () => {
        form.postal = form.postal.replace(/[^0-9]/g, '').slice(0, 7);
    };

    /**
     * 郵便番号に基づいて住所を取得し、更新する。
     */
    const fetchAddress = () => {
        new YubinBangoCore(String(form.postal), (value) => {
            form.address = value.region + value.locality + value.street;
        });
    };

    /**
     * 住所に基づいて都道府県IDを更新する。
     */
    const updatePrefectureId = () => {
        const prefecture = props.prefectures.find(p => form.address.startsWith(p.name));
        form.prefecture_id = prefecture ? prefecture.id : null;
    };

    /**
     * 店舗の説明欄で許可される最大文字数。
     *
     * @type {number} - 最大文字数
     */
    const maxCharacters = 125;

    /**
     * 説明文の残り文字数を計算する算出プロパティ。
     *
     * @returns {number} - 残りの文字数
     */
    const remainingCharacters = computed(() =>
        maxCharacters - (form.description?.length || 0)
    );

    /**
     * 店舗画像のプレビュー用のURLを提供するリアクティブなプロパティ。
     *
     *  @type {string} - 店舗画像のファイルパス
     */
    let imagePreview = form.restaurant_image ? ref('/storage/images/' + form.restaurant_image) : ref(null);

    /**
     * ファイル選択時に呼び出され、画像プレビューを更新する。
     *
     * @param {Event} event - ファイル入力イベント
     */
    const handleFileChange = (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => imagePreview.value = e.target.result;
            reader.readAsDataURL(file);
        }
    };

    return {
        selectedGenreName,
        validatePhoneNumber,
        validatePostal,
        fetchAddress,
        updatePrefectureId,
        remainingCharacters,
        imagePreview,
        handleFileChange,
    };
}
