'*********************************************************
'Full-Hoas, the code should be formatted for better look
'*********************************************************
Set objArgs = WScript.Arguments 
Dim t 
t = Now
Dim Input

Set fso = CreateObject("Scripting.FileSystemObject")

InputFolderJob = "c:\Users\Public\xampp\htdocs\"
InputFolderHome = "c:\xampp\htdocs\"

REM Input = "1"'InputBox("PICK A NUMBER for the path (1-c:\Users\Public\xampp, 2-c:\xampp)","zip","1") 
REM if Input="1" then 
	REM InputFolder = "c:\Users\Public\xampp\htdocs\" 
REM elseif Input="2" then 
	REM InputFolder = "c:\xampp\htdocs\" 
REM Else
	REM Wscript.Quit
REM end if
If (fso.FolderExists(InputFolderJob)) Then
	InputFolder = InputFolderJob
ElseIf (fso.FolderExists(InputFolderHome)) Then
	InputFolder = InputFolderHome
Else
	MsgBox "No Job-Folder and No Home-Folder"
	Wscript.Quit
End If 

'*********************************************
'START MESSAGE for USER
Set objExplorer = CreateObject("InternetExplorer.Application")
objExplorer.Navigate "about:blank"
objExplorer.ToolBar = 0
objExplorer.StatusBar = 0
objExplorer.Left = 400
objExplorer.Top = 200
objExplorer.Width = 400
objExplorer.Height = 200
objExplorer.Visible = 1             
objExplorer.Document.Body.Style.Cursor = "wait"
objExplorer.Document.Title = "New Version"
objExplorer.Document.Body.InnerHTML = "New version in process: " & InputFolder & " -> "
'*********************************************


Set wss = CreateObject("WScript.Shell")
'remove cache
If (fso.FolderExists(InputFolder & "quiz\var\cache\")) Then
	Set folder = fso.GetFolder(InputFolder & "quiz\var\cache\" )
	' delete all files in  folder
	for each f in folder.Files
	   On Error Resume Next
	   name = f.name
	   f.Delete True
	   If Err Then
		 WScript.Echo "Error deleting:" & Name & " - " & Err.Description
	   Else
		 'wss.Popup Name & " file Deleted", 1, "Progress" ' show message box for a second and close
		 'WScript.Echo "Deleted:" & Name
	   End If
	   On Error GoTo 0
	Next
	' delete all subfolders and files
	For Each f In folder.SubFolders
	   On Error Resume Next
	   name = f.name
	   f.Delete True
	   If Err Then
		 WScript.Echo "Error deleting:" & Name & " - " & Err.Description
	   Else
		 'wss.Popup Name & " folder Deleted", 1, "Progress" ' show message box for a second and close
		 'WScript.Echo "Deleted:" & Name
	   End If
	   On Error GoTo 0
	Next
End If

'remove logs
If (fso.FolderExists(InputFolder & "quiz\var\logs\")) Then
	Set folder = fso.GetFolder(InputFolder & "quiz\var\logs\" )
	' delete all files in  folder
	for each f in folder.Files
	   On Error Resume Next
	   name = f.name
	   f.Delete True
	   If Err Then
		 WScript.Echo "Error deleting:" & Name & " - " & Err.Description
	   Else
		 'wss.Popup Name & " file Deleted", 1, "Progress" ' show message box for a second and close
		 'WScript.Echo "Deleted:" & Name
	   End If
	   On Error GoTo 0
	Next
End If


'remove sessions
If (fso.FolderExists(InputFolder & "quiz\var\sessions\")) Then
	Set folder = fso.GetFolder(InputFolder & "quiz\var\sessions\" )
	' delete all files in  folder
	for each f in folder.Files
	   On Error Resume Next
	   name = f.name
	   f.Delete True
	   If Err Then
		 WScript.Echo "Error deleting:" & Name & " - " & Err.Description
	   End If
	   On Error GoTo 0
	Next
	' delete all subfolders and files
	For Each f In folder.SubFolders
	   On Error Resume Next
	   name = f.name
	   f.Delete True
	   If Err Then
		 WScript.Echo "Error deleting:" & Name & " - " & Err.Description
	   End If
	   On Error GoTo 0
	Next
End If


'search new folder to move files
Dim inr
Dim i
Dim sfnNew
Set folder = fso.GetFolder(InputFolder & "")
inr=10
For i = 200 to 10 Step -1
	If (fso.FolderExists(InputFolder & "quiz" & i)) Then
      sfnNew = InputFolder & "quiz" & (i+1)
	  fso.CreateFolder(sfnNew)
	  wss.Popup " New folder is created " & sfnNew, 1, "Progress" ' show message box for a second and close
	  Exit For
   End If
Next
'**************************************
objExplorer.Document.Body.InnerHTML = "New version in process: " & InputFolder & " -> " & sfnNew
Wscript.Sleep 500
'**************************************

'move folder to new folder (quizXXX)
Set folder = fso.GetFolder(InputFolder & "quiz\")
Set colSubfolders = folder.Subfolders
For Each sf in colSubfolders
	If (fso.FolderExists(sf & "\")) Then
		If sf.Name <> "vendor" And sf.Name <> "images" Then
			'wss.Popup sf  & " moved to " & sfnNew & "\", 1, "Progress" ' show message box for a second and close
			fso.MoveFolder sf, sfnNew & "\"
			'wss.Popup sf.Name & " moved to " & sfnNew & "\", 1, "Progress" ' show message box for a second and close
		End If
	End If
Next
'move files to new folder (quizXXX)
Set colFiles = folder.Files
For Each f in colFiles
	'wss.Popup f  & " moved to " & sfnNew & "\", 1, "Progress" ' show message box for a second and close
    fso.MoveFile f, sfnNew & "\"
Next

'start zip-process
wss.Run sfnNew & "\v_zip.vbs" 
' Using Set is mandatory
Set wss = Nothing
'**************************************************************
objExplorer.Document.Body.InnerHTML = "New version is complete. Close it"
objExplorer.Document.Body.Style.Cursor = "default"
Wscript.Sleep 5000
objExplorer.Quit

