Const FOF_SILENT = &H4&
Const FOF_RENAMEONCOLLISION = &H8&
Const FOF_NOCONFIRMATION = &H10&
Const FOF_ALLOWUNDO = &H40&
Const FOF_FILESONLY = &H80&
Const FOF_SIMPLEPROGRESS = &H100&
Const FOF_NOCONFIRMMKDIR = &H200&
Const FOF_NOERRORUI = &H400&
Const FOF_NOCOPYSECURITYATTRIBS = &H800&
Const FOF_NORECURSION = &H1000&
Const FOF_NO_CONNECTED_ELEMENTS = &H2000&

cFlags = FOF_SILENT + FOF_NOCONFIRMATION + FOF_NOERRORUI

Set objArgs = WScript.Arguments 
Dim t 
t = Now
Dim Input

Set fso = CreateObject("Scripting.FileSystemObject")

InputFolderJob = "c:\Users\Public\xampp\htdocs\"
ZipFileJob = "c:\Users\Public\php\"

InputFolderHome = "c:\xampp\htdocs\"
ZipFileHome = "c:\Users\astk\Google Drive\php\"

REM Input = "2" 'InputBox("PICK A NUMBER for the path (1-c:\Users\Public\xampp, 2-c:\xampp)","zip","2") 
REM if Input="1" then 
	REM InputFolder = "c:\Users\Public\xampp\htdocs\" 
	REM ZipFile = "c:\Users\Public\php\" 
REM elseif Input="2" then 
	REM InputFolder = "c:\xampp\htdocs\" 
	REM ZipFile = "c:\Users\astk\Google Drive\php\" 
REM Else
	REM Wscript.Quit
REM end if
Input = ""

If (fso.FolderExists(InputFolderJob)) Then
	InputFolder = InputFolderJob
	ZipFile = ZipFileJob
ElseIf (fso.FolderExists(InputFolderHome)) Then
	InputFolder = InputFolderHome
	ZipFile = ZipFileHome
Else
	MsgBox "No Job-Folder and No Home-Folder"
	Wscript.Quit
End If 


strPath = Wscript.ScriptFullName
'Set WshShell = WScript.CreateObject("WScript.Shell")
Set objFSO  = CreateObject("Scripting.FileSystemObject")
'strCurDir    = WshShell.CurrentDirectory
Set objFile = objFSO.GetFile(strPath)
strFolder = objFSO.GetParentFolderName(objFile) 
search = "\"
InputFolder = InputFolder & right (strFolder, len(strFolder)-instrrev (strFolder, search)) & search

'Set objFile = objFSO.GetFile(strFolder)

REM Input = InputBox("Name of folder with last version quizXX","zip","quiz-"+strFolder ) 
REM if Input<>"" then 
	REM InputFolder = InputFolder & Input & "\" 
REM Else
	REM Wscript.Quit
REM end if
Input = ""

abc = "a b c d e f g h i j k m n o p q r s t u v w x y z"
aabc = Split(abc," ")
bCreatedToday = False
Input = "quiz-" & Right("0" & Day(t),2) & Right("0" & Month(t),2)
For i = Ubound(aabc) To 0 Step -1
	If (fso.FileExists(ZipFile & Input & aabc(i) & ".zip" )) Then
		'MsgBox ZipFile & Input & aabc(i) & ".zip" 
		Input = Input & aabc(i+1)
		bCreatedToday = True
		Exit For
	End If
Next

If bCreatedToday = False Then
	Input = Input & "a"
End If

'Input = InputBox("Enter zip name, only name, zip to -> " & ZipFile,"zip from " & InputFolder, Input) 
if Input<>"" then 
	ZipFile = ZipFile & Input & ".zip" 
Else
	Wscript.Quit
end if
Input = ""

If (fso.FolderExists(InputFolder & ".git\")) Then
	set objFSO = CreateObject("Scripting.FileSystemObject")
	set objFolder = objFSO.GetFolder(InputFolder & ".git\")
	CONST HIDDEN = 2
	If (objFolder.Attributes AND HIDDEN) then
		objFolder.Attributes = 0
	End if
	set objFolder = objFSO.GetFolder(InputFolder & ".up\")
	If (objFolder.Attributes AND HIDDEN) then
		objFolder.Attributes = 0
	End if
End if

With CreateObject("Scripting.FileSystemObject")
	zipFile = .GetAbsolutePathName(zipFile)
	InputFolder = .GetAbsolutePathName(InputFolder)

	With .CreateTextFile(zipFile, True)
		.Write Chr(80) & Chr(75) & Chr(5) & Chr(6) & String(18, chr(0))
	End With
End With

With CreateObject("Shell.Application")
	'MsgBox .NameSpace(InputFolder).Items.Count
	'Wscript.Quit
	.NameSpace(zipFile).CopyHere .NameSpace(InputFolder).Items, 16

	Do Until .NameSpace(zipFile).Items.Count = _
			 .NameSpace(InputFolder).Items.Count
		WScript.Sleep 1000 
	Loop
End With
wScript.Sleep 2000 


