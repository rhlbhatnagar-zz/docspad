/**
 * This Thrift file can be included by other Thrift files that want to share
 * these definitions.
 */

namespace * docspad.client

typedef i32 int

enum DocspadOperation {
  UPLOAD = 1,
  DOWNLOAD = 2,

}

struct Document {
  1:string filename,
  2:string contents,
}

exception InvalidOperation {
  1: int what,
  2: string why
}

service Docspad {
  string upload(1:Document uploaded),
  string download(1:string filename) throws (1:InvalidOperation thrown),
  int ping()
}
