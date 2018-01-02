/**
 * Part of KgF.
 *
 * MIT License
 * Copyright (c) 2017 LordKorea
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */
"use strict";

(function(){
  /**
   * Changes the name of the user.
   */
  function changeName() {
    var newName = prompt("What name would you like to have?")
    if (newName === null) {
      return
    }
    if (newName === "" || newName.length >= 32) {
      alert("Invalid name! Please use between 1 and 31 characters.")
      return
    }
    $("#username").text(newName)
    $.ajax({
      method: "POST",
      url: "/options",
      data: {name: newName}
    })
  }

  $("#nameChangeLabel").click(changeName)
})()
