import sys
from importlib import import_module

arguments = sys.argv

if(arguments[1] == "method"):
    if(argument[2] == "moduleread"):
        moduleselect = import_module(arguments[3])
        print(getattr(moduleselect, arguments[2]))
elif(arguments[1] == "function"):
    pass