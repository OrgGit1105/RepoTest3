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

export const GLOBAL_PRIVILEGES = {
  data: [
    {
      id: 1, name: 'Select',
    },
    {
      id: 2, name: 'Insert',
    },
    {
      id: 3, name: 'Update',
    },
    {
      id: 4, name: 'Delete',
    },
    {
      id: 5, name: 'File',
    },
  ],
  structure: [
    {
      id: 6, name: 'Create',
    },
    {
      id: 7, name: 'Alter',
    },
    {
      id: 8, name: 'Index',
    },
    {
      id: 9, name: 'Drop',
    },
    {
      id: 10, name: 'Create temporary tables',
    },
    {
      id: 11, name: 'Show view',
    },
    {
      id: 12, name: 'Create routine',
    },
    {
      id: 13, name: 'Alter routine',
    },
    {
      id: 14, name: 'Execute',
    },
    {
      id: 15, name: 'Create view',
    },
    {
      id: 16, name: 'Event',
    },
    {
      id: 17, name: 'Trigger',
    },
  ],
  administrator: [
    {
      id: 18, name: 'Grant',
    },
    {
      id: 19, name: 'Super',
    },
    {
      id: 20, name: 'Process',
    },
    {
      id: 21, name: 'Reload',
    },
    {
      id: 22, name: 'Shutdown',
    },
    {
      id: 23, name: 'Show databases',
    },
    {
      id: 24, name: 'Lock tables',
    },
    {
      id: 25, name: 'References',
    },
    {
      id: 26, name: 'Replication client',
    },
    {
      id: 27, name: 'Replication slave',
    },
    {
      id: 28, name: 'Create user',
    },
  ],
};

export const DATABASES = {
  data: [
    {
      id: 1, name: 'Select',
    },
    {
      id: 2, name: 'Insert',
    },
    {
      id: 3, name: 'Update',
    },
    {
      id: 4, name: 'Delete',
    },
  ],
  structure: [
    {
      id: 5, name: 'Create',
    },
    {
      id: 6, name: 'Alter',
    },
    {
      id: 7, name: 'Index',
    },
    {
      id: 8, name: 'Drop',
    },
    {
      id: 9, name: 'Create temporary tables',
    },
    {
      id: 10, name: 'Show view',
    },
    {
      id: 11, name: 'Create routine',
    },
    {
      id: 12, name: 'Alter routine',
    },
    {
      id: 13, name: 'Execute',
    },
    {
      id: 14, name: 'Create view',
    },
    {
      id: 15, name: 'Event',
    },
    {
      id: 16, name: 'Trigger',
    },
  ],
  administrator: [
    {
      id: 17, name: 'Grant',
    },
    {
      id: 18, name: 'Lock tables',
    },
    {
      id: 19, name: 'References',
    },
  ],
};
