
## 実装

### Task
- TaskBuilder のテンプレート
- 基本タスクをここき書き込んでおく
- Task::exec() or Task::buider()->exec() で実行可能
- buider() で addEvent() をしたり、ミニ処理を追加したりできる
  - TODO: テンプレートの前に実行する処理は今はできない
- instance 化はできないが、常に新しい instance を生成する

### TaskBuilder
- Task処理の組み立てと実行を行う
- `TaskFlow`
  - Job 系を格納(Taskの最小単位)
  - TaskBuilder ごとに独立
- `EventManager`
  - Event 系を格納
  - Task 内で一つだけ

### TaskEventer
- Task, Job 実行時に起きるイベントを管理
- 発火、統計管理
- Task 実行時に生成する
  - その時 `EventManager` を引き継ぐ
