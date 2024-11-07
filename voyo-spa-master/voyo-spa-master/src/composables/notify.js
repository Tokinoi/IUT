import { Notify } from 'quasar'

export function notify(content, type) {
  Notify.create({
    message: content,
    type: type
  })
}
