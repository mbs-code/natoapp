import dateFnsFormat from 'date-fns/format'
import formatDistanceToNow from 'date-fns/formatDistanceToNow'
import differenceInDays from 'date-fns/differenceInDays'
import { ja } from 'date-fns/locale'
import formatDuration from 'format-duration'

// ref: https://www.softel.co.jp/blogs/tech/archives/6099
const urlReg = new RegExp(
  /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim
)

// ref: https://qiita.com/icchi_h/items/a438f2f33aaf15e74059
const atReg = new RegExp(/([@＠][A-Za-z0-9._-]+)/gm)
const hashReg = new RegExp(/([#＃][Ａ-Ｚａ-ｚA-Za-z一-鿆0-9０-９ぁ-ヶｦ-ﾟー._-]+)/gm)

/// ////////////////////////////////////////

/**
 * 3桁ごとにカンマを入れる.
 *
 * @param {number|string} num 数字文字列
 * @return {string} 3桁区切りの数字
 */
const numberDigit = function (num) {
  try {
    return Number(num).toLocaleString()
  } catch (err) {
    return num
  }
}

/**
 * 小数点を固定する.
 *
 * @param {number|string} num 数字文字列
 * @param {number|0} digit 桁数
 * @return {string} 3桁区切りの数字
 */
const numberToFixed = function (num, digit = 0) {
  try {
    return Number(num).toFixed(digit)
  } catch (err) {
    return num
  }
}

/**
 * duration数値を分かりやすく変換する.
 *
 * @param {number|string} num duration 文字列
 * @return {string} HH:MM:SS
 */
const durationHumanized = function (num) {
  try {
    return formatDuration(Number(num) * 1000)
  } catch (err) {
    return datetimeUTC
  }
}

/**
 * テキスト中にリンクを貼る.
 *
 * @param {string} text テキスト
 * @param {object} options オプション
 * @param {string} options.hash ハッシュタグのリンク先 [twitter|youtube]
 * @return {string} v-html 用のテキスト
 */
const htmlLinker = function (str, options = {}) {
  try {
    const text = str
      .replace(urlReg, (match) => {
        const escape = encodeURI(match)
        return `<a href='${escape}' target='_blank'>${match}</a>`
      })
      .replace(atReg, (match) => {
        const raw = match.substring(1) // @を外す
        const escape = encodeURIComponent(raw)
        return `<a href='https://twitter.com/${escape}' target='_blank'>${match}</a>`
      })
      .replace(hashReg, (match) => {
        const escape = encodeURIComponent(match)
        if (options.hash === 'youtube') {
          return `<a href='https://www.youtube.com/results?search_query=${escape}' target='_blank'>${match}</a>`
        } else {
          // default は twitter
          return `<a href='https://twitter.com/search?q=${escape}' target='_blank'>${match}</a>`
        }
      })
      .replace(/\r?\n|\r/g, '<br>')
    return text
  } catch (err) {
    return str
  }
}

/// ////////////////////////////////////////
// 日付系
/// ////////////////////////////////////////

/**
 * 日時をパースする.
 *
 * doc: https://date-fns.org/v2.16.1/docs/format
 * @param {string} datetimeUTC UTC日時文字列
 * @param {string|null} format format文字列
 * @return {string} 日時文字列
 */
const toDatetime = function (datetimeUTC, format = 'yyyy-MM-dd HH:mm:ss') {
  try {
    const date = new Date(datetimeUTC) // ここで timezone 処理も行われる？
    return dateFnsFormat(date, format)
  } catch (err) {
    return datetimeUTC
  }
}

/**
 * 現在時刻との差を分かりやすく変換する.
 *
 * @param {string} datetimeUTC UTC日時文字列
 * @return {string} xx日
 */
const datetimeHumanuzed = function (datetimeUTC) {
  try {
    const date = new Date(datetimeUTC)
    return formatDistanceToNow(date, { addSuffix: true, locale: ja })
  } catch (err) {
    return datetimeUTC
  }
}

/**
 * 現在時刻との日数差に変換する.
 *
 * @param {string} datetimeUTC UTC日時文字列
 * @return {string} xx日前, xx時間後など
 */
const daysToNow = function (datetimeUTC) {
  try {
    const date = new Date(datetimeUTC)
    return differenceInDays(new Date(), date) + '日'
  } catch (err) {
    return datetimeUTC
  }
}

/// ////////////////////////////////////////

/**
 * 文字列変換を format 文字列基準で行うヘルパー.
 *
 * replaceAll() を噛ますので必要なとこのみで.
 * @param {string} datetimeUTC UTC日時文字列
 * @param {string} format format文字列
 *   - F: toDatetime
 *   - H: datetimeHumanuzed
 *   - D: daysToNow
 * @param {string|null} formatDatetime datetimeのformat文字列
 * @return {string} 変換文字列
 */
const formatHelper = function (datetimeUTC, format, formatDatetime = undefined) {
  let str = format
  str = str.replaceAll('DF', toDatetime(datetimeUTC, formatDatetime))
  str = str.replaceAll('DH', datetimeHumanuzed(datetimeUTC))
  str = str.replaceAll('DN', daysToNow(datetimeUTC))
  return str
}

export default {
  filters: {
    numberDigit,
    numberToFixed,
    durationHumanized,
    htmlLinker,

    toDatetime,
    datetimeHumanuzed,
    daysToNow,
    formatHelper,
  },
}
