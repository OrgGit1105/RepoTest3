export const UserRoleId = {
  HEAD_QUARTER: 1,
  DEPARTMENT: 2,
};

export const AuthorityList = [
  { value: '', text: '権限をを入力してください', disabled: true },
  { value: 1, text: '本部' },
  { value: 2, text: '拠点' },
];
export const DepartmentList = [
  { value: 1, text: 'First Department' },
  { value: 2, text: 'Second Department' },
  { value: 3, text: 'Third Department' },
];

export const InterviewDeparuteOption = [
  { value: 1, text: 'Head Quarter' },
  { value: 2, text: 'Department' },
];
export const CompanyOption = [
  { value: '', text: '面接拠点--', disabled: true },
  { value: 1, text: '本部' },
  { value: 2, text: '拠点' },
];
export const SpouseOption = [
  { value: '', text: '配偶者を入力してください', disabled: true },
  { value: 1, text: 'あり' },
  { value: 0, text: 'なし' },
];
export const NumberOfWorkHistoryOption = [{ value: '', text: '職歴数を入力してください', disabled: true }, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
export const NumberDependentsOption = [{ value: '', text: '扶養人数を入力してください', disabled: true }, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
export const FinalEducationOption = [
  { value: '', text: '最終学歴を入力してください', disabled: true },
  { value: 1, text: '小学校' },
  { value: 2, text: '中学校' },
  { value: 3, text: '高等学校' },
  { value: 4, text: '専門学校' },
  { value: 5, text: '高等専門学校' },
  { value: 6, text: '短期大学' },
  { value: 7, text: '大学' },
];
export const Spouse = {
  NO: 0,
  YES: 1,
};
export const InterviewBranch = {
  HEAD_QUARTER: 1,
  DEPARTMENT: 2,
};
export const CompanyBranch = [
  { value: 'First Department', text: 'First Department' },
  { value: 'Second Department', text: 'Second Department' },
  { value: 'Third Department', text: 'Third Department' },
];
export const GLOBAL_PRIVILEGES_DATA = [
  'Select', 'Insert', 'Update', 'Delete', 'File',
];

export const GLOBAL_PRIVILEGES_STRUCTURE = [
  'Create', 'Alter', 'Index', 'Drop', 'Create temporary tables',
];

export const GLOBAL_PRIVILEGES_ADMINISTRATOR = [
  'Grant', 'Super', 'Process', 'Reload',
];
