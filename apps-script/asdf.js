function execute(...args) {
  let file = DriveApp.getFileById('193LyU22aWd4T65E86IYQ45RSvRXnWEG7O13EtPrUVR4')

  let newFile = file.makeCopy('this is the copy of will cover')

  findAndReplace(newFile.getId(), args)

  let url = createPDF(newFile.getId())

  newFile.setTrashed(true)

  return url
}

function findAndReplace(documentId, data) {
  const requests = []

  for (const item of data) {
    const request = {
      replaceAllText: {
        containsText: {
          text: item.find,
          matchCase: true,
        },
        replaceText: item.replace,
      },
    }
    requests.push(request)
  }

  try {
    const response = Docs.Documents.batchUpdate({ requests: requests }, documentId)

    const replies = response.replies

    for (const [index] of replies.entries()) {
      const numReplacements = replies[index].replaceAllText.occurrencesChanged || 0

      Logger.log('Request %s performed %s replacements.', index, numReplacements)
    }
  } catch (e) {
    Logger.log('Failed with error : %s', e.message)
  }
}

function createPDF(documentId) {
  const url = `https://docs.google.com/document/d/${documentId}/export?format=pdf`

  const params = { method: 'GET', headers: { authorization: 'Bearer ' + ScriptApp.getOAuthToken() } }

  const blob = UrlFetchApp.fetch(url, params).getBlob().setName('this is pdf file of copied file.pdf')

  const folder = DriveApp.getFileById(documentId).getParents().next()

  let file = folder.createFile(blob)

  return file.getDownloadUrl()

  // file.setSharing(DriveApp.Access.ANYONE, DriveApp.Permission.VIEW)

  // return file.getUrl()
}

function merge() {
  let file = DriveApp.getFileById('193LyU22aWd4T65E86IYQ45RSvRXnWEG7O13EtPrUVR4')

  let newFile = file.makeCopy('this is the copy of will cover')

  let file1 = DocumentApp.openById(newFile.getId())

  let file2 = DocumentApp.openById('1DdZUNdaAX-3nvM6hLjWvqAcvc362iz8e6LPrscI_50M')
  const content = getContent(file1.getBody(), file2.getBody())

  //     const docTargetBody = file1.getBody();
  //   docTargetBody.appendHorizontalRule();
  // docTargetBody.appendParagraph('').appendText(`${docName}`).setLinkUrl(docHtml)
}

function getContent(body1, body2) {
  var totalElements = body2.getNumChildren()

  for (var j = 0; j < totalElements; ++j) {
    var paragraph = body.getChild(j).copy()

    body1.appendParagraph(paragraph)
  }
}

// this is only for reference
function mergeGoogleDocs() {
  var docIDs = ['documentID_1', 'documentID_2', 'documentID_3', 'documentID_4']
  var baseDoc = DocumentApp.openById(docIDs[0])

  var body = baseDoc.getActiveSection()

  for (var i = 1; i < docIDs.length; ++i) {
    var otherBody = DocumentApp.openById(docIDs[i]).getActiveSection()
    var totalElements = otherBody.getNumChildren()
    for (var j = 0; j < totalElements; ++j) {
      var element = otherBody.getChild(j).copy()
      var type = element.getType()
      if (type == DocumentApp.ElementType.PARAGRAPH) body.appendParagraph(element)
      else if (type == DocumentApp.ElementType.TABLE) body.appendTable(element)
      else if (type == DocumentApp.ElementType.LIST_ITEM) body.appendListItem(element)
      else throw new Error('Unknown element type: ' + type)
    }
  }
}
