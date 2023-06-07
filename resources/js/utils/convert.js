var encoding = require('encoding-japanese');
export const processText = (inputText) => {
  const output = [];
  const json = inputText.split(' ');
  json.forEach((item) => {
    output.push(item.replace(/\'/g, '').split(/(\d+)/).filter(Boolean));
  });
  for (let i = 0; i < output.length; i++) {
    if (output[i].length > 0) {
      return output[i];
    }
  }
};

export const convertJapanese = (text) => {
  const unicodeString = encoding.convert(text, {
    to: 'UNICODE',
    from: 'UTF8',
    type: 'string' });
  return unicodeString;
};

export const convertUTF8 = (text) => {
  const unicodeString = encoding.convert(text, {
    to: 'UTF8',
    from: 'SJIS-win',
    type: 'string' });
  return unicodeString;
};

export const renderMonthName = (name) => {
  // console.log('Name', name);
  switch (name) {
    case 1:
      return 'Jan';

    case 2:
      return 'Feb';

    case 3:
      return 'Mar';

    case 4:
      return 'Apr';

    case 5:
      return 'May';

    case 6:
      return 'Jun';

    case 7:
      return 'Jul';

    case 8:
      return 'Aug';

    case 9:
      return 'Sep';

    case 10:
      return 'Oct';

    case 11:
      return 'Nov';

    case 12:
      return 'Dec';

    default:
  }
};
export const renderEducationNameToNumber = (education) => {
  switch (education) {
    case '小学校':
      return 0;
    case '中学校':
      return 1;
    case '高等学校':
      return 2;
    case '専門学校':
      return 3;
    case '高等専門学校':
      return 4;
    case '短期大学':
      return 5;
    case '大学':
      return 6;
    default:
  }
};
export const checkFormatDate = (dateString) => {
  var regEx = /^\d{4}-\d{2}-\d{2}$/;
  return dateString.match(regEx) != null;
};
export const renderEducationName = (education) => {
  switch (education) {
    case 0:
      return '小学校';
    case 1:
      return '中学校';
    case 2:
      return '高等学校';
    case 3:
      return '専門学校';
    case 4:
      return '高等専門学校';
    case 5:
      return '短期大学';
    case 6:
      return '大学';
    default:
  }
};

export const convertSpouse = (spouse) => {
  switch (spouse) {
    case 1:
      return 'あり';
    case 0:
      return 'なし';
    default:
  }
};

export const renderSpouseToNumber = (spouse) => {
  switch (spouse) {
    case 'あり':
      return 1;
    case 'なし':
      return 0;
    default:
  }
};
