On Error Resume Next
strComputer = "."
Set objWMIService = GetObject("Winmgmts:\\" & strComputer & "\root\cimv2")
Set colItems = objWMIService.ExecQuery("Select * From Win32_DesktopMonitor")
For Each objItem in colItems
    intHorizontal = objItem.ScreenWidth
    intVertical = objItem.ScreenHeight
Next
Set objExplorer = CreateObject _
    ("InternetExplorer.Application")
objExplorer.Navigate "about:blank"
objExplorer.ToolBar = 0
objExplorer.StatusBar = 0
objExplorer.Left = (intHorizontal-400)/2
objExplorer.Top = (intVertical-200)/2
objExplorer.Width = 400
objExplorer.Height = 200
objExplorer.Visible = 1             
objExplorer.Document.Body.Style.Cursor = "wait"
objExplorer.Document.Title = "Logon script in progress"
objExplorer.Document.Body.InnerHTML = "Your logon script is being processed. " _
    & "This might take several minutes to complete."
	
Wscript.Sleep 10000

objExplorer.Document.Body.InnerHTML = "Your logon script is now complete."
objExplorer.Document.Body.Style.Cursor = "default"
Wscript.Sleep 5000
objExplorer.Quit