/**
 * This Thrift file can be included by other Thrift files that want to share
 * these definitions.
 */

namespace * docspad.client

typedef i32 int

struct document {
  1: int key
  2: string value
}

service Docspad {
  int upload(1: int uploaded)
}
