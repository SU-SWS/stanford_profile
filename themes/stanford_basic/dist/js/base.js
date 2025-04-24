/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 62:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// https://github.com/sebmarkbage/ecmascript-string-left-right-trim
__webpack_require__(629)('trimLeft', function ($trim) {
  return function trimLeft() {
    return $trim(this, 1);
  };
}, 'trimStart');


/***/ }),

/***/ 107:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var anObject = __webpack_require__(4228);
var toPrimitive = __webpack_require__(3048);
var NUMBER = 'number';

module.exports = function (hint) {
  if (hint !== 'string' && hint !== NUMBER && hint !== 'default') throw TypeError('Incorrect hint');
  return toPrimitive(anObject(this), hint != NUMBER);
};


/***/ }),

/***/ 128:
/***/ (function(module) {

module.exports = function (exec) {
  try {
    return { e: false, v: exec() };
  } catch (e) {
    return { e: true, v: e };
  }
};


/***/ }),

/***/ 157:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var toInteger = __webpack_require__(7087);
var max = Math.max;
var min = Math.min;
module.exports = function (index, length) {
  index = toInteger(index);
  return index < 0 ? max(index + length, 0) : min(index, length);
};


/***/ }),

/***/ 177:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";
// 21.1.3.18 String.prototype.startsWith(searchString [, position ])

var $export = __webpack_require__(2127);
var toLength = __webpack_require__(1485);
var context = __webpack_require__(8942);
var STARTS_WITH = 'startsWith';
var $startsWith = ''[STARTS_WITH];

$export($export.P + $export.F * __webpack_require__(5203)(STARTS_WITH), 'String', {
  startsWith: function startsWith(searchString /* , position = 0 */) {
    var that = context(this, searchString, STARTS_WITH);
    var index = toLength(Math.min(arguments.length > 1 ? arguments[1] : undefined, that.length));
    var search = String(searchString);
    return $startsWith
      ? $startsWith.call(that, search, index)
      : that.slice(index, index + search.length) === search;
  }
});


/***/ }),

/***/ 210:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.1.2.6 Number.MAX_SAFE_INTEGER
var $export = __webpack_require__(2127);

$export($export.S, 'Number', { MAX_SAFE_INTEGER: 0x1fffffffffffff });


/***/ }),

/***/ 237:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var global = __webpack_require__(7526);
var hide = __webpack_require__(3341);
var uid = __webpack_require__(4415);
var TYPED = uid('typed_array');
var VIEW = uid('view');
var ABV = !!(global.ArrayBuffer && global.DataView);
var CONSTR = ABV;
var i = 0;
var l = 9;
var Typed;

var TypedArrayConstructors = (
  'Int8Array,Uint8Array,Uint8ClampedArray,Int16Array,Uint16Array,Int32Array,Uint32Array,Float32Array,Float64Array'
).split(',');

while (i < l) {
  if (Typed = global[TypedArrayConstructors[i++]]) {
    hide(Typed.prototype, TYPED, true);
    hide(Typed.prototype, VIEW, true);
  } else CONSTR = false;
}

module.exports = {
  ABV: ABV,
  CONSTR: CONSTR,
  TYPED: TYPED,
  VIEW: VIEW
};


/***/ }),

/***/ 333:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(7209)('Uint8', 1, function (init) {
  return function Uint8ClampedArray(data, byteOffset, length) {
    return init(this, data, byteOffset, length);
  };
}, true);


/***/ }),

/***/ 341:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var isRegExp = __webpack_require__(5411);
var anObject = __webpack_require__(4228);
var speciesConstructor = __webpack_require__(9190);
var advanceStringIndex = __webpack_require__(8828);
var toLength = __webpack_require__(1485);
var callRegExpExec = __webpack_require__(2535);
var regexpExec = __webpack_require__(9600);
var fails = __webpack_require__(9448);
var $min = Math.min;
var $push = [].push;
var $SPLIT = 'split';
var LENGTH = 'length';
var LAST_INDEX = 'lastIndex';
var MAX_UINT32 = 0xffffffff;

// babel-minify transpiles RegExp('x', 'y') -> /x/y and it causes SyntaxError
var SUPPORTS_Y = !fails(function () { RegExp(MAX_UINT32, 'y'); });

// @@split logic
__webpack_require__(9228)('split', 2, function (defined, SPLIT, $split, maybeCallNative) {
  var internalSplit;
  if (
    'abbc'[$SPLIT](/(b)*/)[1] == 'c' ||
    'test'[$SPLIT](/(?:)/, -1)[LENGTH] != 4 ||
    'ab'[$SPLIT](/(?:ab)*/)[LENGTH] != 2 ||
    '.'[$SPLIT](/(.?)(.?)/)[LENGTH] != 4 ||
    '.'[$SPLIT](/()()/)[LENGTH] > 1 ||
    ''[$SPLIT](/.?/)[LENGTH]
  ) {
    // based on es5-shim implementation, need to rework it
    internalSplit = function (separator, limit) {
      var string = String(this);
      if (separator === undefined && limit === 0) return [];
      // If `separator` is not a regex, use native split
      if (!isRegExp(separator)) return $split.call(string, separator, limit);
      var output = [];
      var flags = (separator.ignoreCase ? 'i' : '') +
                  (separator.multiline ? 'm' : '') +
                  (separator.unicode ? 'u' : '') +
                  (separator.sticky ? 'y' : '');
      var lastLastIndex = 0;
      var splitLimit = limit === undefined ? MAX_UINT32 : limit >>> 0;
      // Make `global` and avoid `lastIndex` issues by working with a copy
      var separatorCopy = new RegExp(separator.source, flags + 'g');
      var match, lastIndex, lastLength;
      while (match = regexpExec.call(separatorCopy, string)) {
        lastIndex = separatorCopy[LAST_INDEX];
        if (lastIndex > lastLastIndex) {
          output.push(string.slice(lastLastIndex, match.index));
          if (match[LENGTH] > 1 && match.index < string[LENGTH]) $push.apply(output, match.slice(1));
          lastLength = match[0][LENGTH];
          lastLastIndex = lastIndex;
          if (output[LENGTH] >= splitLimit) break;
        }
        if (separatorCopy[LAST_INDEX] === match.index) separatorCopy[LAST_INDEX]++; // Avoid an infinite loop
      }
      if (lastLastIndex === string[LENGTH]) {
        if (lastLength || !separatorCopy.test('')) output.push('');
      } else output.push(string.slice(lastLastIndex));
      return output[LENGTH] > splitLimit ? output.slice(0, splitLimit) : output;
    };
  // Chakra, V8
  } else if ('0'[$SPLIT](undefined, 0)[LENGTH]) {
    internalSplit = function (separator, limit) {
      return separator === undefined && limit === 0 ? [] : $split.call(this, separator, limit);
    };
  } else {
    internalSplit = $split;
  }

  return [
    // `String.prototype.split` method
    // https://tc39.github.io/ecma262/#sec-string.prototype.split
    function split(separator, limit) {
      var O = defined(this);
      var splitter = separator == undefined ? undefined : separator[SPLIT];
      return splitter !== undefined
        ? splitter.call(separator, O, limit)
        : internalSplit.call(String(O), separator, limit);
    },
    // `RegExp.prototype[@@split]` method
    // https://tc39.github.io/ecma262/#sec-regexp.prototype-@@split
    //
    // NOTE: This cannot be properly polyfilled in engines that don't support
    // the 'y' flag.
    function (regexp, limit) {
      var res = maybeCallNative(internalSplit, regexp, this, limit, internalSplit !== $split);
      if (res.done) return res.value;

      var rx = anObject(regexp);
      var S = String(this);
      var C = speciesConstructor(rx, RegExp);

      var unicodeMatching = rx.unicode;
      var flags = (rx.ignoreCase ? 'i' : '') +
                  (rx.multiline ? 'm' : '') +
                  (rx.unicode ? 'u' : '') +
                  (SUPPORTS_Y ? 'y' : 'g');

      // ^(? + rx + ) is needed, in combination with some S slicing, to
      // simulate the 'y' flag.
      var splitter = new C(SUPPORTS_Y ? rx : '^(?:' + rx.source + ')', flags);
      var lim = limit === undefined ? MAX_UINT32 : limit >>> 0;
      if (lim === 0) return [];
      if (S.length === 0) return callRegExpExec(splitter, S) === null ? [S] : [];
      var p = 0;
      var q = 0;
      var A = [];
      while (q < S.length) {
        splitter.lastIndex = SUPPORTS_Y ? q : 0;
        var z = callRegExpExec(splitter, SUPPORTS_Y ? S : S.slice(q));
        var e;
        if (
          z === null ||
          (e = $min(toLength(splitter.lastIndex + (SUPPORTS_Y ? 0 : q)), S.length)) === p
        ) {
          q = advanceStringIndex(S, q, unicodeMatching);
        } else {
          A.push(S.slice(p, q));
          if (A.length === lim) return A;
          for (var i = 1; i <= z.length - 1; i++) {
            A.push(z[i]);
            if (A.length === lim) return A;
          }
          q = p = e;
        }
      }
      A.push(S.slice(p));
      return A;
    }
  ];
});


/***/ }),

/***/ 345:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.2.2.28 Math.sign(x)
var $export = __webpack_require__(2127);

$export($export.S, 'Math', { sign: __webpack_require__(3733) });


/***/ }),

/***/ 489:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

var dP = (__webpack_require__(7967).f);
var FProto = Function.prototype;
var nameRE = /^\s*function ([^ (]*)/;
var NAME = 'name';

// 19.2.4.2 name
NAME in FProto || __webpack_require__(1763) && dP(FProto, NAME, {
  configurable: true,
  get: function () {
    try {
      return ('' + this).match(nameRE)[1];
    } catch (e) {
      return '';
    }
  }
});


/***/ }),

/***/ 521:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// https://github.com/sebmarkbage/ecmascript-string-left-right-trim
__webpack_require__(629)('trimRight', function ($trim) {
  return function trimRight() {
    return $trim(this, 2);
  };
}, 'trimEnd');


/***/ }),

/***/ 571:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

var $export = __webpack_require__(2127);
var $parseInt = __webpack_require__(2738);
// 18.2.5 parseInt(string, radix)
$export($export.G + $export.F * (parseInt != $parseInt), { parseInt: $parseInt });


/***/ }),

/***/ 619:
/***/ (function() {

// if NodeList doesn't support forEach, use Array's forEach()
NodeList.prototype.forEach = NodeList.prototype.forEach || Array.prototype.forEach;


/***/ }),

/***/ 627:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// 19.1.2.9 / 15.2.3.2 Object.getPrototypeOf(O)
var has = __webpack_require__(7917);
var toObject = __webpack_require__(8270);
var IE_PROTO = __webpack_require__(766)('IE_PROTO');
var ObjectProto = Object.prototype;

module.exports = Object.getPrototypeOf || function (O) {
  O = toObject(O);
  if (has(O, IE_PROTO)) return O[IE_PROTO];
  if (typeof O.constructor == 'function' && O instanceof O.constructor) {
    return O.constructor.prototype;
  } return O instanceof Object ? ObjectProto : null;
};


/***/ }),

/***/ 629:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var $export = __webpack_require__(2127);
var defined = __webpack_require__(3344);
var fails = __webpack_require__(9448);
var spaces = __webpack_require__(832);
var space = '[' + spaces + ']';
var non = '\u200b\u0085';
var ltrim = RegExp('^' + space + space + '*');
var rtrim = RegExp(space + space + '*$');

var exporter = function (KEY, exec, ALIAS) {
  var exp = {};
  var FORCE = fails(function () {
    return !!spaces[KEY]() || non[KEY]() != non;
  });
  var fn = exp[KEY] = FORCE ? exec(trim) : spaces[KEY];
  if (ALIAS) exp[ALIAS] = fn;
  $export($export.P + $export.F * FORCE, 'String', exp);
};

// 1 -> String#trimLeft
// 2 -> String#trimRight
// 3 -> String#trim
var trim = exporter.trim = function (string, TYPE) {
  string = String(defined(string));
  if (TYPE & 1) string = string.replace(ltrim, '');
  if (TYPE & 2) string = string.replace(rtrim, '');
  return string;
};

module.exports = exporter;


/***/ }),

/***/ 660:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

var $export = __webpack_require__(2127);
$export($export.G + $export.W + $export.F * !(__webpack_require__(237).ABV), {
  DataView: (__webpack_require__(8032).DataView)
});


/***/ }),

/***/ 752:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// 7.1.1 ToPrimitive(input [, PreferredType])
var isObject = __webpack_require__(4401);
// instead of the ES6 spec version, we didn't implement @@toPrimitive case
// and the second argument - flag - preferred type is a string
module.exports = function (it, S) {
  if (!isObject(it)) return it;
  var fn, val;
  if (S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it))) return val;
  if (typeof (fn = it.valueOf) == 'function' && !isObject(val = fn.call(it))) return val;
  if (!S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it))) return val;
  throw TypeError("Can't convert object to primitive value");
};


/***/ }),

/***/ 762:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var classof = __webpack_require__(4848);
var ITERATOR = __webpack_require__(7574)('iterator');
var Iterators = __webpack_require__(906);
module.exports = (__webpack_require__(6094).getIteratorMethod) = function (it) {
  if (it != undefined) return it[ITERATOR]
    || it['@@iterator']
    || Iterators[classof(it)];
};


/***/ }),

/***/ 766:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var shared = __webpack_require__(4556)('keys');
var uid = __webpack_require__(4415);
module.exports = function (key) {
  return shared[key] || (shared[key] = uid(key));
};


/***/ }),

/***/ 812:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var isObject = __webpack_require__(4401);
module.exports = function (it) {
  if (!isObject(it)) throw TypeError(it + ' is not an object!');
  return it;
};


/***/ }),

/***/ 832:
/***/ (function(module) {

module.exports = '\x09\x0A\x0B\x0C\x0D\x20\xA0\u1680\u180E\u2000\u2001\u2002\u2003' +
  '\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028\u2029\uFEFF';


/***/ }),

/***/ 906:
/***/ (function(module) {

module.exports = {};


/***/ }),

/***/ 923:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// most Object methods by ES6 should accept primitives
var $export = __webpack_require__(2127);
var core = __webpack_require__(6094);
var fails = __webpack_require__(9448);
module.exports = function (KEY, exec) {
  var fn = (core.Object || {})[KEY] || Object[KEY];
  var exp = {};
  exp[KEY] = exec(fn);
  $export($export.S + $export.F * fails(function () { fn(1); }), 'Object', exp);
};


/***/ }),

/***/ 935:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

var $export = __webpack_require__(2127);
// 19.1.2.2 / 15.2.3.5 Object.create(O [, Properties])
$export($export.S, 'Object', { create: __webpack_require__(4719) });


/***/ }),

/***/ 957:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// 21.1.3.25 String.prototype.trim()
__webpack_require__(629)('trim', function ($trim) {
  return function trim() {
    return $trim(this, 3);
  };
});


/***/ }),

/***/ 1060:
/***/ (function(__unused_webpack_module, exports) {

exports.f = Object.getOwnPropertySymbols;


/***/ }),

/***/ 1104:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 26.1.8 Reflect.getPrototypeOf(target)
var $export = __webpack_require__(2127);
var getProto = __webpack_require__(627);
var anObject = __webpack_require__(4228);

$export($export.S, 'Reflect', {
  getPrototypeOf: function getPrototypeOf(target) {
    return getProto(anObject(target));
  }
});


/***/ }),

/***/ 1124:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// https://github.com/tc39/proposal-global
var $export = __webpack_require__(8535);

$export($export.G, { global: __webpack_require__(6670) });


/***/ }),

/***/ 1158:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// 21.2.5.3 get RegExp.prototype.flags
var anObject = __webpack_require__(4228);
module.exports = function () {
  var that = anObject(this);
  var result = '';
  if (that.global) result += 'g';
  if (that.ignoreCase) result += 'i';
  if (that.multiline) result += 'm';
  if (that.unicode) result += 'u';
  if (that.sticky) result += 'y';
  return result;
};


/***/ }),

/***/ 1212:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var toInteger = __webpack_require__(7087);
var defined = __webpack_require__(3344);
// true  -> String#at
// false -> String#codePointAt
module.exports = function (TO_STRING) {
  return function (that, pos) {
    var s = String(defined(that));
    var i = toInteger(pos);
    var l = s.length;
    var a, b;
    if (i < 0 || i >= l) return TO_STRING ? '' : undefined;
    a = s.charCodeAt(i);
    return a < 0xd800 || a > 0xdbff || i + 1 === l || (b = s.charCodeAt(i + 1)) < 0xdc00 || b > 0xdfff
      ? TO_STRING ? s.charAt(i) : a
      : TO_STRING ? s.slice(i, i + 2) : (a - 0xd800 << 10) + (b - 0xdc00) + 0x10000;
  };
};


/***/ }),

/***/ 1220:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(7209)('Int16', 2, function (init) {
  return function Int16Array(data, byteOffset, length) {
    return init(this, data, byteOffset, length);
  };
});


/***/ }),

/***/ 1243:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(7146);
module.exports = __webpack_require__(6094).Object.entries;


/***/ }),

/***/ 1249:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// fallback for non-array-like ES3 and non-enumerable old V8 strings
var cof = __webpack_require__(5089);
// eslint-disable-next-line no-prototype-builtins
module.exports = Object('z').propertyIsEnumerable(0) ? Object : function (it) {
  return cof(it) == 'String' ? it.split('') : Object(it);
};


/***/ }),

/***/ 1308:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var document = (__webpack_require__(7526).document);
module.exports = document && document.documentElement;


/***/ }),

/***/ 1311:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// 19.1.2.14 / 15.2.3.14 Object.keys(O)
var $keys = __webpack_require__(4561);
var enumBugKeys = __webpack_require__(6140);

module.exports = Object.keys || function keys(O) {
  return $keys(O, enumBugKeys);
};


/***/ }),

/***/ 1318:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.2.2.16 Math.fround(x)
var $export = __webpack_require__(2127);

$export($export.S, 'Math', { fround: __webpack_require__(2122) });


/***/ }),

/***/ 1368:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(62);
module.exports = __webpack_require__(6094).String.trimLeft;


/***/ }),

/***/ 1384:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var global = __webpack_require__(7526);
var macrotask = (__webpack_require__(2780).set);
var Observer = global.MutationObserver || global.WebKitMutationObserver;
var process = global.process;
var Promise = global.Promise;
var isNode = __webpack_require__(5089)(process) == 'process';

module.exports = function () {
  var head, last, notify;

  var flush = function () {
    var parent, fn;
    if (isNode && (parent = process.domain)) parent.exit();
    while (head) {
      fn = head.fn;
      head = head.next;
      try {
        fn();
      } catch (e) {
        if (head) notify();
        else last = undefined;
        throw e;
      }
    } last = undefined;
    if (parent) parent.enter();
  };

  // Node.js
  if (isNode) {
    notify = function () {
      process.nextTick(flush);
    };
  // browsers with MutationObserver, except iOS Safari - https://github.com/zloirock/core-js/issues/339
  } else if (Observer && !(global.navigator && global.navigator.standalone)) {
    var toggle = true;
    var node = document.createTextNode('');
    new Observer(flush).observe(node, { characterData: true }); // eslint-disable-line no-new
    notify = function () {
      node.data = toggle = !toggle;
    };
  // environments with maybe non-completely correct, but existent Promise
  } else if (Promise && Promise.resolve) {
    // Promise.resolve without an argument throws an error in LG WebOS 2
    var promise = Promise.resolve(undefined);
    notify = function () {
      promise.then(flush);
    };
  // for other environments - macrotask based on:
  // - setImmediate
  // - MessageChannel
  // - window.postMessag
  // - onreadystatechange
  // - setTimeout
  } else {
    notify = function () {
      // strange IE + webpack dev server bug - use .call(global)
      macrotask.call(global, flush);
    };
  }

  return function (fn) {
    var task = { fn: fn, next: undefined };
    if (last) last.next = task;
    if (!head) {
      head = task;
      notify();
    } last = task;
  };
};


/***/ }),

/***/ 1430:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 19.1.3.1 Object.assign(target, source)
var $export = __webpack_require__(2127);

$export($export.S + $export.F, 'Object', { assign: __webpack_require__(8206) });


/***/ }),

/***/ 1449:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var $export = __webpack_require__(2127);
var $reduce = __webpack_require__(6543);

$export($export.P + $export.F * !__webpack_require__(6884)([].reduce, true), 'Array', {
  // 22.1.3.18 / 15.4.4.21 Array.prototype.reduce(callbackfn [, initialValue])
  reduce: function reduce(callbackfn /* , initialValue */) {
    return $reduce(this, callbackfn, arguments.length, arguments[1], false);
  }
});


/***/ }),

/***/ 1458:
/***/ (function() {

// if NodeList doesn't support forEach, use Array's forEach()
NodeList.prototype.forEach = NodeList.prototype.forEach || Array.prototype.forEach;

/***/ }),

/***/ 1464:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// false -> Array#indexOf
// true  -> Array#includes
var toIObject = __webpack_require__(7221);
var toLength = __webpack_require__(1485);
var toAbsoluteIndex = __webpack_require__(157);
module.exports = function (IS_INCLUDES) {
  return function ($this, el, fromIndex) {
    var O = toIObject($this);
    var length = toLength(O.length);
    var index = toAbsoluteIndex(fromIndex, length);
    var value;
    // Array#includes uses SameValueZero equality algorithm
    // eslint-disable-next-line no-self-compare
    if (IS_INCLUDES && el != el) while (length > index) {
      value = O[index++];
      // eslint-disable-next-line no-self-compare
      if (value != value) return true;
    // Array#indexOf ignores holes, Array#includes - not
    } else for (;length > index; index++) if (IS_INCLUDES || index in O) {
      if (O[index] === el) return IS_INCLUDES || index || 0;
    } return !IS_INCLUDES && -1;
  };
};


/***/ }),

/***/ 1473:
/***/ (function(module) {

// 20.2.2.20 Math.log1p(x)
module.exports = Math.log1p || function log1p(x) {
  return (x = +x) > -1e-8 && x < 1e-8 ? x - x * x / 2 : Math.log(1 + x);
};


/***/ }),

/***/ 1485:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// 7.1.15 ToLength
var toInteger = __webpack_require__(7087);
var min = Math.min;
module.exports = function (it) {
  return it > 0 ? min(toInteger(it), 0x1fffffffffffff) : 0; // pow(2, 53) - 1 == 9007199254740991
};


/***/ }),

/***/ 1508:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// check on default Array iterator
var Iterators = __webpack_require__(906);
var ITERATOR = __webpack_require__(7574)('iterator');
var ArrayProto = Array.prototype;

module.exports = function (it) {
  return it !== undefined && (Iterators.Array === it || ArrayProto[ITERATOR] === it);
};


/***/ }),

/***/ 1626:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var dP = __webpack_require__(7967);
var anObject = __webpack_require__(4228);
var getKeys = __webpack_require__(1311);

module.exports = __webpack_require__(1763) ? Object.defineProperties : function defineProperties(O, Properties) {
  anObject(O);
  var keys = getKeys(Properties);
  var length = keys.length;
  var i = 0;
  var P;
  while (length > i) dP.f(O, P = keys[i++], Properties[P]);
  return O;
};


/***/ }),

/***/ 1632:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var strong = __webpack_require__(6197);
var validate = __webpack_require__(2888);
var SET = 'Set';

// 23.2 Set Objects
module.exports = __webpack_require__(8933)(SET, function (get) {
  return function Set() { return get(this, arguments.length > 0 ? arguments[0] : undefined); };
}, {
  // 23.2.3.1 Set.prototype.add(value)
  add: function add(value) {
    return strong.def(validate(this, SET), value = value === 0 ? 0 : value, value);
  }
}, strong);


/***/ }),

/***/ 1763:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// Thank's IE8 for his funny defineProperty
module.exports = !__webpack_require__(9448)(function () {
  return Object.defineProperty({}, 'a', { get: function () { return 7; } }).a != 7;
});


/***/ }),

/***/ 1879:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 26.1.6 Reflect.get(target, propertyKey [, receiver])
var gOPD = __webpack_require__(8641);
var getPrototypeOf = __webpack_require__(627);
var has = __webpack_require__(7917);
var $export = __webpack_require__(2127);
var isObject = __webpack_require__(3305);
var anObject = __webpack_require__(4228);

function get(target, propertyKey /* , receiver */) {
  var receiver = arguments.length < 3 ? target : arguments[2];
  var desc, proto;
  if (anObject(target) === receiver) return target[propertyKey];
  if (desc = gOPD.f(target, propertyKey)) return has(desc, 'value')
    ? desc.value
    : desc.get !== undefined
      ? desc.get.call(receiver)
      : undefined;
  if (isObject(proto = getPrototypeOf(target))) return get(proto, propertyKey, receiver);
}

$export($export.S, 'Reflect', { get: get });


/***/ }),

/***/ 1883:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 26.1.9 Reflect.has(target, propertyKey)
var $export = __webpack_require__(2127);

$export($export.S, 'Reflect', {
  has: function has(target, propertyKey) {
    return propertyKey in target;
  }
});


/***/ }),

/***/ 1895:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 19.1.2.7 Object.getOwnPropertyNames(O)
__webpack_require__(923)('getOwnPropertyNames', function () {
  return (__webpack_require__(4765).f);
});


/***/ }),

/***/ 1933:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.1.2.2 Number.isFinite(number)
var $export = __webpack_require__(2127);
var _isFinite = (__webpack_require__(7526).isFinite);

$export($export.S, 'Number', {
  isFinite: function isFinite(it) {
    return typeof it == 'number' && _isFinite(it);
  }
});


/***/ }),

/***/ 1984:
/***/ (function(module) {

module.exports = function (exec) {
  try {
    return !!exec();
  } catch (e) {
    return true;
  }
};


/***/ }),

/***/ 1996:
/***/ (function(module) {

module.exports = function (bitmap, value) {
  return {
    enumerable: !(bitmap & 1),
    configurable: !(bitmap & 2),
    writable: !(bitmap & 4),
    value: value
  };
};


/***/ }),

/***/ 2087:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(7209)('Uint16', 2, function (init) {
  return function Uint16Array(data, byteOffset, length) {
    return init(this, data, byteOffset, length);
  };
});


/***/ }),

/***/ 2122:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// 20.2.2.16 Math.fround(x)
var sign = __webpack_require__(3733);
var pow = Math.pow;
var EPSILON = pow(2, -52);
var EPSILON32 = pow(2, -23);
var MAX32 = pow(2, 127) * (2 - EPSILON32);
var MIN32 = pow(2, -126);

var roundTiesToEven = function (n) {
  return n + 1 / EPSILON - 1 / EPSILON;
};

module.exports = Math.fround || function fround(x) {
  var $abs = Math.abs(x);
  var $sign = sign(x);
  var a, result;
  if ($abs < MIN32) return $sign * roundTiesToEven($abs / MIN32 / EPSILON32) * MIN32 * EPSILON32;
  a = (1 + EPSILON32 / EPSILON) * $abs;
  result = a - (a - $abs);
  // eslint-disable-next-line no-self-compare
  if (result > MAX32 || result != result) return $sign * Infinity;
  return $sign * result;
};


/***/ }),

/***/ 2127:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var global = __webpack_require__(7526);
var core = __webpack_require__(6094);
var hide = __webpack_require__(3341);
var redefine = __webpack_require__(8859);
var ctx = __webpack_require__(5052);
var PROTOTYPE = 'prototype';

var $export = function (type, name, source) {
  var IS_FORCED = type & $export.F;
  var IS_GLOBAL = type & $export.G;
  var IS_STATIC = type & $export.S;
  var IS_PROTO = type & $export.P;
  var IS_BIND = type & $export.B;
  var target = IS_GLOBAL ? global : IS_STATIC ? global[name] || (global[name] = {}) : (global[name] || {})[PROTOTYPE];
  var exports = IS_GLOBAL ? core : core[name] || (core[name] = {});
  var expProto = exports[PROTOTYPE] || (exports[PROTOTYPE] = {});
  var key, own, out, exp;
  if (IS_GLOBAL) source = name;
  for (key in source) {
    // contains in native
    own = !IS_FORCED && target && target[key] !== undefined;
    // export native or passed
    out = (own ? target : source)[key];
    // bind timers to global for call from export context
    exp = IS_BIND && own ? ctx(out, global) : IS_PROTO && typeof out == 'function' ? ctx(Function.call, out) : out;
    // extend global
    if (target) redefine(target, key, out, type & $export.U);
    // export
    if (exports[key] != out) hide(exports, key, exp);
    if (IS_PROTO && expProto[key] != out) expProto[key] = out;
  }
};
global.core = core;
// type bitmap
$export.F = 1;   // forced
$export.G = 2;   // global
$export.S = 4;   // static
$export.P = 8;   // proto
$export.B = 16;  // bind
$export.W = 32;  // wrap
$export.U = 64;  // safe
$export.R = 128; // real proto method for `library`
module.exports = $export;


/***/ }),

/***/ 2220:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

var $export = __webpack_require__(2127);
var toAbsoluteIndex = __webpack_require__(157);
var fromCharCode = String.fromCharCode;
var $fromCodePoint = String.fromCodePoint;

// length should be 1, old FF problem
$export($export.S + $export.F * (!!$fromCodePoint && $fromCodePoint.length != 1), 'String', {
  // 21.1.2.2 String.fromCodePoint(...codePoints)
  fromCodePoint: function fromCodePoint(x) { // eslint-disable-line no-unused-vars
    var res = [];
    var aLen = arguments.length;
    var i = 0;
    var code;
    while (aLen > i) {
      code = +arguments[i++];
      if (toAbsoluteIndex(code, 0x10ffff) !== code) throw RangeError(code + ' is not a valid code point');
      res.push(code < 0x10000
        ? fromCharCode(code)
        : fromCharCode(((code -= 0x10000) >> 10) + 0xd800, code % 0x400 + 0xdc00)
      );
    } return res.join('');
  }
});


/***/ }),

/***/ 2322:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// https://tc39.github.io/proposal-flatMap/#sec-FlattenIntoArray
var isArray = __webpack_require__(7981);
var isObject = __webpack_require__(3305);
var toLength = __webpack_require__(1485);
var ctx = __webpack_require__(5052);
var IS_CONCAT_SPREADABLE = __webpack_require__(7574)('isConcatSpreadable');

function flattenIntoArray(target, original, source, sourceLen, start, depth, mapper, thisArg) {
  var targetIndex = start;
  var sourceIndex = 0;
  var mapFn = mapper ? ctx(mapper, thisArg, 3) : false;
  var element, spreadable;

  while (sourceIndex < sourceLen) {
    if (sourceIndex in source) {
      element = mapFn ? mapFn(source[sourceIndex], sourceIndex, original) : source[sourceIndex];

      spreadable = false;
      if (isObject(element)) {
        spreadable = element[IS_CONCAT_SPREADABLE];
        spreadable = spreadable !== undefined ? !!spreadable : isArray(element);
      }

      if (spreadable && depth > 0) {
        targetIndex = flattenIntoArray(target, original, element, toLength(element.length), targetIndex, depth - 1) - 1;
      } else {
        if (targetIndex >= 0x1fffffffffffff) throw TypeError();
        target[targetIndex] = element;
      }

      targetIndex++;
    }
    sourceIndex++;
  }
  return targetIndex;
}

module.exports = flattenIntoArray;


/***/ }),

/***/ 2335:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.2.2.9 Math.cbrt(x)
var $export = __webpack_require__(2127);
var sign = __webpack_require__(3733);

$export($export.S, 'Math', {
  cbrt: function cbrt(x) {
    return sign(x = +x) * Math.pow(Math.abs(x), 1 / 3);
  }
});


/***/ }),

/***/ 2346:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var $export = __webpack_require__(2127);
var toObject = __webpack_require__(8270);
var toPrimitive = __webpack_require__(3048);

$export($export.P + $export.F * __webpack_require__(9448)(function () {
  return new Date(NaN).toJSON() !== null
    || Date.prototype.toJSON.call({ toISOString: function () { return 1; } }) !== 1;
}), 'Date', {
  // eslint-disable-next-line no-unused-vars
  toJSON: function toJSON(key) {
    var O = toObject(this);
    var pv = toPrimitive(O);
    return typeof pv == 'number' && !isFinite(pv) ? null : O.toISOString();
  }
});


/***/ }),

/***/ 2392:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.2.2.7 Math.atanh(x)
var $export = __webpack_require__(2127);
var $atanh = Math.atanh;

// Tor Browser bug: Math.atanh(-0) -> 0
$export($export.S + $export.F * !($atanh && 1 / $atanh(-0) < 0), 'Math', {
  atanh: function atanh(x) {
    return (x = +x) == 0 ? x : Math.log((1 + x) / (1 - x)) / 2;
  }
});


/***/ }),

/***/ 2405:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var $export = __webpack_require__(2127);
var $at = __webpack_require__(1212)(false);
$export($export.P, 'String', {
  // 21.1.3.3 String.prototype.codePointAt(pos)
  codePointAt: function codePointAt(pos) {
    return $at(this, pos);
  }
});


/***/ }),

/***/ 2419:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(9650);
__webpack_require__(935);
__webpack_require__(6064);
__webpack_require__(7067);
__webpack_require__(2642);
__webpack_require__(3000);
__webpack_require__(8647);
__webpack_require__(1895);
__webpack_require__(8236);
__webpack_require__(3822);
__webpack_require__(5572);
__webpack_require__(9318);
__webpack_require__(5032);
__webpack_require__(9073);
__webpack_require__(1430);
__webpack_require__(8451);
__webpack_require__(8132);
__webpack_require__(7482);
__webpack_require__(5049);
__webpack_require__(489);
__webpack_require__(5502);
__webpack_require__(571);
__webpack_require__(6108);
__webpack_require__(4509);
__webpack_require__(7727);
__webpack_require__(6701);
__webpack_require__(4419);
__webpack_require__(1933);
__webpack_require__(3157);
__webpack_require__(9497);
__webpack_require__(4104);
__webpack_require__(210);
__webpack_require__(6576);
__webpack_require__(4437);
__webpack_require__(8050);
__webpack_require__(6648);
__webpack_require__(5771);
__webpack_require__(2392);
__webpack_require__(2335);
__webpack_require__(4896);
__webpack_require__(4521);
__webpack_require__(9147);
__webpack_require__(1318);
__webpack_require__(4352);
__webpack_require__(5327);
__webpack_require__(7509);
__webpack_require__(5909);
__webpack_require__(9584);
__webpack_require__(345);
__webpack_require__(9134);
__webpack_require__(7901);
__webpack_require__(6592);
__webpack_require__(2220);
__webpack_require__(3483);
__webpack_require__(957);
__webpack_require__(2975);
__webpack_require__(2405);
__webpack_require__(7224);
__webpack_require__(8872);
__webpack_require__(4894);
__webpack_require__(177);
__webpack_require__(7360);
__webpack_require__(9011);
__webpack_require__(4591);
__webpack_require__(7334);
__webpack_require__(7083);
__webpack_require__(9213);
__webpack_require__(8437);
__webpack_require__(9839);
__webpack_require__(6549);
__webpack_require__(2818);
__webpack_require__(8543);
__webpack_require__(3559);
__webpack_require__(4153);
__webpack_require__(3292);
__webpack_require__(2346);
__webpack_require__(9429);
__webpack_require__(7849);
__webpack_require__(8951);
__webpack_require__(7899);
__webpack_require__(3863);
__webpack_require__(4570);
__webpack_require__(6511);
__webpack_require__(5853);
__webpack_require__(7075);
__webpack_require__(3504);
__webpack_require__(4913);
__webpack_require__(9813);
__webpack_require__(8892);
__webpack_require__(8888);
__webpack_require__(1449);
__webpack_require__(7874);
__webpack_require__(4609);
__webpack_require__(3706);
__webpack_require__(9620);
__webpack_require__(7762);
__webpack_require__(5144);
__webpack_require__(5369);
__webpack_require__(6209);
__webpack_require__(5165);
__webpack_require__(8301);
__webpack_require__(4116);
__webpack_require__(8604);
__webpack_require__(9638);
__webpack_require__(4040);
__webpack_require__(8305);
__webpack_require__(4701);
__webpack_require__(341);
__webpack_require__(6517);
__webpack_require__(3386);
__webpack_require__(1632);
__webpack_require__(9397);
__webpack_require__(8163);
__webpack_require__(5706);
__webpack_require__(660);
__webpack_require__(8699);
__webpack_require__(4702);
__webpack_require__(333);
__webpack_require__(1220);
__webpack_require__(2087);
__webpack_require__(8066);
__webpack_require__(8537);
__webpack_require__(7925);
__webpack_require__(2490);
__webpack_require__(7103);
__webpack_require__(2586);
__webpack_require__(2552);
__webpack_require__(4376);
__webpack_require__(5153);
__webpack_require__(1879);
__webpack_require__(2650);
__webpack_require__(1104);
__webpack_require__(1883);
__webpack_require__(5433);
__webpack_require__(5000);
__webpack_require__(5932);
__webpack_require__(5443);
__webpack_require__(6316);
module.exports = __webpack_require__(6094);


/***/ }),

/***/ 2468:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var $export = __webpack_require__(2127);
var fails = __webpack_require__(9448);
var defined = __webpack_require__(3344);
var quot = /"/g;
// B.2.3.2.1 CreateHTML(string, tag, attribute, value)
var createHTML = function (string, tag, attribute, value) {
  var S = String(defined(string));
  var p1 = '<' + tag;
  if (attribute !== '') p1 += ' ' + attribute + '="' + String(value).replace(quot, '&quot;') + '"';
  return p1 + '>' + S + '</' + tag + '>';
};
module.exports = function (NAME, exec) {
  var O = {};
  O[NAME] = exec(createHTML);
  $export($export.P + $export.F * fails(function () {
    var test = ''[NAME]('"');
    return test !== test.toLowerCase() || test.split('"').length > 3;
  }), 'String', O);
};


/***/ }),

/***/ 2484:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

module.exports = !__webpack_require__(8219) && !__webpack_require__(1984)(function () {
  return Object.defineProperty(__webpack_require__(3802)('div'), 'a', { get: function () { return 7; } }).a != 7;
});


/***/ }),

/***/ 2490:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(7209)('Float64', 8, function (init) {
  return function Float64Array(data, byteOffset, length) {
    return init(this, data, byteOffset, length);
  };
});


/***/ }),

/***/ 2535:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var classof = __webpack_require__(4848);
var builtinExec = RegExp.prototype.exec;

 // `RegExpExec` abstract operation
// https://tc39.github.io/ecma262/#sec-regexpexec
module.exports = function (R, S) {
  var exec = R.exec;
  if (typeof exec === 'function') {
    var result = exec.call(R, S);
    if (typeof result !== 'object') {
      throw new TypeError('RegExp exec method returned something other than an Object or null');
    }
    return result;
  }
  if (classof(R) !== 'RegExp') {
    throw new TypeError('RegExp#exec called on incompatible receiver');
  }
  return builtinExec.call(R, S);
};


/***/ }),

/***/ 2552:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 26.1.3 Reflect.defineProperty(target, propertyKey, attributes)
var dP = __webpack_require__(7967);
var $export = __webpack_require__(2127);
var anObject = __webpack_require__(4228);
var toPrimitive = __webpack_require__(3048);

// MS Edge has broken Reflect.defineProperty - throwing instead of returning false
$export($export.S + $export.F * __webpack_require__(9448)(function () {
  // eslint-disable-next-line no-undef
  Reflect.defineProperty(dP.f({}, 1, { value: 1 }), 1, { value: 2 });
}), 'Reflect', {
  defineProperty: function defineProperty(target, propertyKey, attributes) {
    anObject(target);
    propertyKey = toPrimitive(propertyKey, true);
    anObject(attributes);
    try {
      dP.f(target, propertyKey, attributes);
      return true;
    } catch (e) {
      return false;
    }
  }
});


/***/ }),

/***/ 2586:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 26.1.2 Reflect.construct(target, argumentsList [, newTarget])
var $export = __webpack_require__(2127);
var create = __webpack_require__(4719);
var aFunction = __webpack_require__(3387);
var anObject = __webpack_require__(4228);
var isObject = __webpack_require__(3305);
var fails = __webpack_require__(9448);
var bind = __webpack_require__(5538);
var rConstruct = ((__webpack_require__(7526).Reflect) || {}).construct;

// MS Edge supports only 2 arguments and argumentsList argument is optional
// FF Nightly sets third argument as `new.target`, but does not create `this` from it
var NEW_TARGET_BUG = fails(function () {
  function F() { /* empty */ }
  return !(rConstruct(function () { /* empty */ }, [], F) instanceof F);
});
var ARGS_BUG = !fails(function () {
  rConstruct(function () { /* empty */ });
});

$export($export.S + $export.F * (NEW_TARGET_BUG || ARGS_BUG), 'Reflect', {
  construct: function construct(Target, args /* , newTarget */) {
    aFunction(Target);
    anObject(args);
    var newTarget = arguments.length < 3 ? Target : aFunction(arguments[2]);
    if (ARGS_BUG && !NEW_TARGET_BUG) return rConstruct(Target, args, newTarget);
    if (Target == newTarget) {
      // w/o altered newTarget, optimization for 0-4 arguments
      switch (args.length) {
        case 0: return new Target();
        case 1: return new Target(args[0]);
        case 2: return new Target(args[0], args[1]);
        case 3: return new Target(args[0], args[1], args[2]);
        case 4: return new Target(args[0], args[1], args[2], args[3]);
      }
      // w/o altered newTarget, lot of arguments case
      var $args = [null];
      $args.push.apply($args, args);
      return new (bind.apply(Target, $args))();
    }
    // with altered newTarget, not support built-in constructors
    var proto = newTarget.prototype;
    var instance = create(isObject(proto) ? proto : Object.prototype);
    var result = Function.apply.call(Target, instance, args);
    return isObject(result) ? result : instance;
  }
});


/***/ }),

/***/ 2642:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 19.1.2.6 Object.getOwnPropertyDescriptor(O, P)
var toIObject = __webpack_require__(7221);
var $getOwnPropertyDescriptor = (__webpack_require__(8641).f);

__webpack_require__(923)('getOwnPropertyDescriptor', function () {
  return function getOwnPropertyDescriptor(it, key) {
    return $getOwnPropertyDescriptor(toIObject(it), key);
  };
});


/***/ }),

/***/ 2650:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 26.1.7 Reflect.getOwnPropertyDescriptor(target, propertyKey)
var gOPD = __webpack_require__(8641);
var $export = __webpack_require__(2127);
var anObject = __webpack_require__(4228);

$export($export.S, 'Reflect', {
  getOwnPropertyDescriptor: function getOwnPropertyDescriptor(target, propertyKey) {
    return gOPD.f(anObject(target), propertyKey);
  }
});


/***/ }),

/***/ 2656:
/***/ (function() {

try {
	// test for scope support
	document.querySelector(':scope *');
} catch (error) {
	(function (ElementPrototype) {
		// scope regex
		var scope = /:scope(?![\w-])/gi;

		// polyfill Element#querySelector
		var querySelectorWithScope = polyfill(ElementPrototype.querySelector);

		ElementPrototype.querySelector = function querySelector(selectors) {
			return querySelectorWithScope.apply(this, arguments);
		};

		// polyfill Element#querySelectorAll
		var querySelectorAllWithScope = polyfill(ElementPrototype.querySelectorAll);

		ElementPrototype.querySelectorAll = function querySelectorAll(selectors) {
			return querySelectorAllWithScope.apply(this, arguments);
		};

		// polyfill Element#matches
		if (ElementPrototype.matches) {
			var matchesWithScope = polyfill(ElementPrototype.matches);

			ElementPrototype.matches = function matches(selectors) {
				return matchesWithScope.apply(this, arguments);
			};
		}

		// polyfill Element#closest
		if (ElementPrototype.closest) {
			var closestWithScope = polyfill(ElementPrototype.closest);

			ElementPrototype.closest = function closest(selectors) {
				return closestWithScope.apply(this, arguments);
			};
		}

		function polyfill(qsa) {
			return function (selectors) {
				// whether the selectors contain :scope
				var hasScope = selectors && scope.test(selectors);

				if (hasScope) {
					// fallback attribute
					var attr = 'q' + Math.floor(Math.random() * 9000000) + 1000000;

					// replace :scope with the fallback attribute
					arguments[0] = selectors.replace(scope, '[' + attr + ']');

					// add the fallback attribute
					this.setAttribute(attr, '');

					// results of the qsa
					var elementOrNodeList = qsa.apply(this, arguments);

					// remove the fallback attribute
					this.removeAttribute(attr);

					// return the results of the qsa
					return elementOrNodeList;
				} else {
					// return the results of the qsa
					return qsa.apply(this, arguments);
				}
			};
		}
	})(Element.prototype);
}


/***/ }),

/***/ 2677:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var dP = __webpack_require__(8423);
var createDesc = __webpack_require__(6260);
module.exports = __webpack_require__(8219) ? function (object, key, value) {
  return dP.f(object, key, createDesc(1, value));
} : function (object, key, value) {
  object[key] = value;
  return object;
};


/***/ }),

/***/ 2681:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(5380);
module.exports = __webpack_require__(6094).String.padStart;


/***/ }),

/***/ 2738:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var $parseInt = (__webpack_require__(7526).parseInt);
var $trim = (__webpack_require__(629).trim);
var ws = __webpack_require__(832);
var hex = /^[-+]?0[xX]/;

module.exports = $parseInt(ws + '08') !== 8 || $parseInt(ws + '0x16') !== 22 ? function parseInt(str, radix) {
  var string = $trim(String(str), 3);
  return $parseInt(string, (radix >>> 0) || (hex.test(string) ? 16 : 10));
} : $parseInt;


/***/ }),

/***/ 2750:
/***/ (function(module) {

module.exports = false;


/***/ }),

/***/ 2780:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var ctx = __webpack_require__(5052);
var invoke = __webpack_require__(4877);
var html = __webpack_require__(1308);
var cel = __webpack_require__(6034);
var global = __webpack_require__(7526);
var process = global.process;
var setTask = global.setImmediate;
var clearTask = global.clearImmediate;
var MessageChannel = global.MessageChannel;
var Dispatch = global.Dispatch;
var counter = 0;
var queue = {};
var ONREADYSTATECHANGE = 'onreadystatechange';
var defer, channel, port;
var run = function () {
  var id = +this;
  // eslint-disable-next-line no-prototype-builtins
  if (queue.hasOwnProperty(id)) {
    var fn = queue[id];
    delete queue[id];
    fn();
  }
};
var listener = function (event) {
  run.call(event.data);
};
// Node.js 0.9+ & IE10+ has setImmediate, otherwise:
if (!setTask || !clearTask) {
  setTask = function setImmediate(fn) {
    var args = [];
    var i = 1;
    while (arguments.length > i) args.push(arguments[i++]);
    queue[++counter] = function () {
      // eslint-disable-next-line no-new-func
      invoke(typeof fn == 'function' ? fn : Function(fn), args);
    };
    defer(counter);
    return counter;
  };
  clearTask = function clearImmediate(id) {
    delete queue[id];
  };
  // Node.js 0.8-
  if (__webpack_require__(5089)(process) == 'process') {
    defer = function (id) {
      process.nextTick(ctx(run, id, 1));
    };
  // Sphere (JS game engine) Dispatch API
  } else if (Dispatch && Dispatch.now) {
    defer = function (id) {
      Dispatch.now(ctx(run, id, 1));
    };
  // Browsers with MessageChannel, includes WebWorkers
  } else if (MessageChannel) {
    channel = new MessageChannel();
    port = channel.port2;
    channel.port1.onmessage = listener;
    defer = ctx(port.postMessage, port, 1);
  // Browsers with postMessage, skip WebWorkers
  // IE8 has postMessage, but it's sync & typeof its postMessage is 'object'
  } else if (global.addEventListener && typeof postMessage == 'function' && !global.importScripts) {
    defer = function (id) {
      global.postMessage(id + '', '*');
    };
    global.addEventListener('message', listener, false);
  // IE8-
  } else if (ONREADYSTATECHANGE in cel('script')) {
    defer = function (id) {
      html.appendChild(cel('script'))[ONREADYSTATECHANGE] = function () {
        html.removeChild(this);
        run.call(id);
      };
    };
  // Rest old browsers
  } else {
    defer = function (id) {
      setTimeout(ctx(run, id, 1), 0);
    };
  }
}
module.exports = {
  set: setTask,
  clear: clearTask
};


/***/ }),

/***/ 2818:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// B.2.3.11 String.prototype.small()
__webpack_require__(2468)('small', function (createHTML) {
  return function small() {
    return createHTML(this, 'small', '', '');
  };
});


/***/ }),

/***/ 2820:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(5392)('asyncIterator');


/***/ }),

/***/ 2888:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var isObject = __webpack_require__(3305);
module.exports = function (it, TYPE) {
  if (!isObject(it) || it._t !== TYPE) throw TypeError('Incompatible receiver, ' + TYPE + ' required!');
  return it;
};


/***/ }),

/***/ 2956:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

module.exports = !__webpack_require__(1763) && !__webpack_require__(9448)(function () {
  return Object.defineProperty(__webpack_require__(6034)('div'), 'a', { get: function () { return 7; } }).a != 7;
});


/***/ }),

/***/ 2975:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var $at = __webpack_require__(1212)(true);

// 21.1.3.27 String.prototype[@@iterator]()
__webpack_require__(8175)(String, 'String', function (iterated) {
  this._t = String(iterated); // target
  this._i = 0;                // next index
// 21.1.5.2.1 %StringIteratorPrototype%.next()
}, function () {
  var O = this._t;
  var index = this._i;
  var point;
  if (index >= O.length) return { value: undefined, done: true };
  point = $at(O, index);
  this._i += point.length;
  return { value: point, done: false };
});


/***/ }),

/***/ 2988:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var META = __webpack_require__(4415)('meta');
var isObject = __webpack_require__(3305);
var has = __webpack_require__(7917);
var setDesc = (__webpack_require__(7967).f);
var id = 0;
var isExtensible = Object.isExtensible || function () {
  return true;
};
var FREEZE = !__webpack_require__(9448)(function () {
  return isExtensible(Object.preventExtensions({}));
});
var setMeta = function (it) {
  setDesc(it, META, { value: {
    i: 'O' + ++id, // object ID
    w: {}          // weak collections IDs
  } });
};
var fastKey = function (it, create) {
  // return primitive with prefix
  if (!isObject(it)) return typeof it == 'symbol' ? it : (typeof it == 'string' ? 'S' : 'P') + it;
  if (!has(it, META)) {
    // can't set metadata to uncaught frozen object
    if (!isExtensible(it)) return 'F';
    // not necessary to add metadata
    if (!create) return 'E';
    // add missing metadata
    setMeta(it);
  // return object ID
  } return it[META].i;
};
var getWeak = function (it, create) {
  if (!has(it, META)) {
    // can't set metadata to uncaught frozen object
    if (!isExtensible(it)) return true;
    // not necessary to add metadata
    if (!create) return false;
    // add missing metadata
    setMeta(it);
  // return hash weak collections IDs
  } return it[META].w;
};
// add metadata on freeze-family methods calling
var onFreeze = function (it) {
  if (FREEZE && meta.NEED && isExtensible(it) && !has(it, META)) setMeta(it);
  return it;
};
var meta = module.exports = {
  KEY: META,
  NEED: false,
  fastKey: fastKey,
  getWeak: getWeak,
  onFreeze: onFreeze
};


/***/ }),

/***/ 3000:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 19.1.2.9 Object.getPrototypeOf(O)
var toObject = __webpack_require__(8270);
var $getPrototypeOf = __webpack_require__(627);

__webpack_require__(923)('getPrototypeOf', function () {
  return function getPrototypeOf(it) {
    return $getPrototypeOf(toObject(it));
  };
});


/***/ }),

/***/ 3048:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// 7.1.1 ToPrimitive(input [, PreferredType])
var isObject = __webpack_require__(3305);
// instead of the ES6 spec version, we didn't implement @@toPrimitive case
// and the second argument - flag - preferred type is a string
module.exports = function (it, S) {
  if (!isObject(it)) return it;
  var fn, val;
  if (S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it))) return val;
  if (typeof (fn = it.valueOf) == 'function' && !isObject(val = fn.call(it))) return val;
  if (!S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it))) return val;
  throw TypeError("Can't convert object to primitive value");
};


/***/ }),

/***/ 3133:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// https://tc39.github.io/ecma262/#sec-toindex
var toInteger = __webpack_require__(7087);
var toLength = __webpack_require__(1485);
module.exports = function (it) {
  if (it === undefined) return 0;
  var number = toInteger(it);
  var length = toLength(number);
  if (number !== length) throw RangeError('Wrong length!');
  return length;
};


/***/ }),

/***/ 3157:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.1.2.3 Number.isInteger(number)
var $export = __webpack_require__(2127);

$export($export.S, 'Number', { isInteger: __webpack_require__(3842) });


/***/ }),

/***/ 3191:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// 9.4.2.3 ArraySpeciesCreate(originalArray, length)
var speciesConstructor = __webpack_require__(3606);

module.exports = function (original, length) {
  return new (speciesConstructor(original))(length);
};


/***/ }),

/***/ 3292:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.3.3.1 / 15.9.4.4 Date.now()
var $export = __webpack_require__(2127);

$export($export.S, 'Date', { now: function () { return new Date().getTime(); } });


/***/ }),

/***/ 3305:
/***/ (function(module) {

module.exports = function (it) {
  return typeof it === 'object' ? it !== null : typeof it === 'function';
};


/***/ }),

/***/ 3341:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var dP = __webpack_require__(7967);
var createDesc = __webpack_require__(1996);
module.exports = __webpack_require__(1763) ? function (object, key, value) {
  return dP.f(object, key, createDesc(1, value));
} : function (object, key, value) {
  object[key] = value;
  return object;
};


/***/ }),

/***/ 3344:
/***/ (function(module) {

// 7.2.1 RequireObjectCoercible(argument)
module.exports = function (it) {
  if (it == undefined) throw TypeError("Can't call method on  " + it);
  return it;
};


/***/ }),

/***/ 3386:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var strong = __webpack_require__(6197);
var validate = __webpack_require__(2888);
var MAP = 'Map';

// 23.1 Map Objects
module.exports = __webpack_require__(8933)(MAP, function (get) {
  return function Map() { return get(this, arguments.length > 0 ? arguments[0] : undefined); };
}, {
  // 23.1.3.6 Map.prototype.get(key)
  get: function get(key) {
    var entry = strong.getEntry(validate(this, MAP), key);
    return entry && entry.v;
  },
  // 23.1.3.9 Map.prototype.set(key, value)
  set: function set(key, value) {
    return strong.def(validate(this, MAP), key === 0 ? 0 : key, value);
  }
}, strong, true);


/***/ }),

/***/ 3387:
/***/ (function(module) {

module.exports = function (it) {
  if (typeof it != 'function') throw TypeError(it + ' is not a function!');
  return it;
};


/***/ }),

/***/ 3415:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(8772);
__webpack_require__(5417);
__webpack_require__(5890);
module.exports = __webpack_require__(6094);


/***/ }),

/***/ 3483:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

var $export = __webpack_require__(2127);
var toIObject = __webpack_require__(7221);
var toLength = __webpack_require__(1485);

$export($export.S, 'String', {
  // 21.1.2.4 String.raw(callSite, ...substitutions)
  raw: function raw(callSite) {
    var tpl = toIObject(callSite.raw);
    var len = toLength(tpl.length);
    var aLen = arguments.length;
    var res = [];
    var i = 0;
    while (len > i) {
      res.push(String(tpl[i++]));
      if (i < aLen) res.push(String(arguments[i]));
    } return res.join('');
  }
});


/***/ }),

/***/ 3504:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var $export = __webpack_require__(2127);
var $forEach = __webpack_require__(6179)(0);
var STRICT = __webpack_require__(6884)([].forEach, true);

$export($export.P + $export.F * !STRICT, 'Array', {
  // 22.1.3.10 / 15.4.4.18 Array.prototype.forEach(callbackfn [, thisArg])
  forEach: function forEach(callbackfn /* , thisArg */) {
    return $forEach(this, callbackfn, arguments[1]);
  }
});


/***/ }),

/***/ 3559:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// B.2.3.13 String.prototype.sub()
__webpack_require__(2468)('sub', function (createHTML) {
  return function sub() {
    return createHTML(this, 'sub', '', '');
  };
});


/***/ }),

/***/ 3589:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var $parseFloat = (__webpack_require__(7526).parseFloat);
var $trim = (__webpack_require__(629).trim);

module.exports = 1 / $parseFloat(__webpack_require__(832) + '-0') !== -Infinity ? function parseFloat(str) {
  var string = $trim(String(str), 3);
  var result = $parseFloat(string);
  return result === 0 && string.charAt(0) == '-' ? -0 : result;
} : $parseFloat;


/***/ }),

/***/ 3606:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var isObject = __webpack_require__(3305);
var isArray = __webpack_require__(7981);
var SPECIES = __webpack_require__(7574)('species');

module.exports = function (original) {
  var C;
  if (isArray(original)) {
    C = original.constructor;
    // cross-realm fallback
    if (typeof C == 'function' && (C === Array || isArray(C.prototype))) C = undefined;
    if (isObject(C)) {
      C = C[SPECIES];
      if (C === null) C = undefined;
    }
  } return C === undefined ? Array : C;
};


/***/ }),

/***/ 3706:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var $export = __webpack_require__(2127);
var toIObject = __webpack_require__(7221);
var toInteger = __webpack_require__(7087);
var toLength = __webpack_require__(1485);
var $native = [].lastIndexOf;
var NEGATIVE_ZERO = !!$native && 1 / [1].lastIndexOf(1, -0) < 0;

$export($export.P + $export.F * (NEGATIVE_ZERO || !__webpack_require__(6884)($native)), 'Array', {
  // 22.1.3.14 / 15.4.4.15 Array.prototype.lastIndexOf(searchElement [, fromIndex])
  lastIndexOf: function lastIndexOf(searchElement /* , fromIndex = @[*-1] */) {
    // convert -0 to +0
    if (NEGATIVE_ZERO) return $native.apply(this, arguments) || 0;
    var O = toIObject(this);
    var length = toLength(O.length);
    var index = length - 1;
    if (arguments.length > 1) index = Math.min(index, toInteger(arguments[1]));
    if (index < 0) index = length + index;
    for (;index >= 0; index--) if (index in O) if (O[index] === searchElement) return index || 0;
    return -1;
  }
});


/***/ }),

/***/ 3733:
/***/ (function(module) {

// 20.2.2.28 Math.sign(x)
module.exports = Math.sign || function sign(x) {
  // eslint-disable-next-line no-self-compare
  return (x = +x) == 0 || x != x ? x : x < 0 ? -1 : 1;
};


/***/ }),

/***/ 3802:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var isObject = __webpack_require__(4401);
var document = (__webpack_require__(6670).document);
// typeof document.createElement is 'object' in old IE
var is = isObject(document) && isObject(document.createElement);
module.exports = function (it) {
  return is ? document.createElement(it) : {};
};


/***/ }),

/***/ 3822:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 19.1.2.17 Object.seal(O)
var isObject = __webpack_require__(3305);
var meta = (__webpack_require__(2988).onFreeze);

__webpack_require__(923)('seal', function ($seal) {
  return function seal(it) {
    return $seal && isObject(it) ? $seal(meta(it)) : it;
  };
});


/***/ }),

/***/ 3842:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// 20.1.2.3 Number.isInteger(number)
var isObject = __webpack_require__(3305);
var floor = Math.floor;
module.exports = function isInteger(it) {
  return !isObject(it) && isFinite(it) && floor(it) === it;
};


/***/ }),

/***/ 3844:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var def = (__webpack_require__(7967).f);
var has = __webpack_require__(7917);
var TAG = __webpack_require__(7574)('toStringTag');

module.exports = function (it, tag, stat) {
  if (it && !has(it = stat ? it : it.prototype, TAG)) def(it, TAG, { configurable: true, value: tag });
};


/***/ }),

/***/ 3854:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var DESCRIPTORS = __webpack_require__(1763);
var getKeys = __webpack_require__(1311);
var toIObject = __webpack_require__(7221);
var isEnum = (__webpack_require__(8449).f);
module.exports = function (isEntries) {
  return function (it) {
    var O = toIObject(it);
    var keys = getKeys(O);
    var length = keys.length;
    var i = 0;
    var result = [];
    var key;
    while (length > i) {
      key = keys[i++];
      if (!DESCRIPTORS || isEnum.call(O, key)) {
        result.push(isEntries ? [key, O[key]] : O[key]);
      }
    }
    return result;
  };
};


/***/ }),

/***/ 3863:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var ctx = __webpack_require__(5052);
var $export = __webpack_require__(2127);
var toObject = __webpack_require__(8270);
var call = __webpack_require__(7368);
var isArrayIter = __webpack_require__(1508);
var toLength = __webpack_require__(1485);
var createProperty = __webpack_require__(7227);
var getIterFn = __webpack_require__(762);

$export($export.S + $export.F * !__webpack_require__(8931)(function (iter) { Array.from(iter); }), 'Array', {
  // 22.1.2.1 Array.from(arrayLike, mapfn = undefined, thisArg = undefined)
  from: function from(arrayLike /* , mapfn = undefined, thisArg = undefined */) {
    var O = toObject(arrayLike);
    var C = typeof this == 'function' ? this : Array;
    var aLen = arguments.length;
    var mapfn = aLen > 1 ? arguments[1] : undefined;
    var mapping = mapfn !== undefined;
    var index = 0;
    var iterFn = getIterFn(O);
    var length, result, step, iterator;
    if (mapping) mapfn = ctx(mapfn, aLen > 2 ? arguments[2] : undefined, 2);
    // if object isn't iterable or it's array with default iterator - use simple case
    if (iterFn != undefined && !(C == Array && isArrayIter(iterFn))) {
      for (iterator = iterFn.call(O), result = new C(); !(step = iterator.next()).done; index++) {
        createProperty(result, index, mapping ? call(iterator, mapfn, [step.value, index], true) : step.value);
      }
    } else {
      length = toLength(O.length);
      for (result = new C(length); length > index; index++) {
        createProperty(result, index, mapping ? mapfn(O[index], index) : O[index]);
      }
    }
    result.length = index;
    return result;
  }
});


/***/ }),

/***/ 4040:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var anObject = __webpack_require__(4228);
var toLength = __webpack_require__(1485);
var advanceStringIndex = __webpack_require__(8828);
var regExpExec = __webpack_require__(2535);

// @@match logic
__webpack_require__(9228)('match', 1, function (defined, MATCH, $match, maybeCallNative) {
  return [
    // `String.prototype.match` method
    // https://tc39.github.io/ecma262/#sec-string.prototype.match
    function match(regexp) {
      var O = defined(this);
      var fn = regexp == undefined ? undefined : regexp[MATCH];
      return fn !== undefined ? fn.call(regexp, O) : new RegExp(regexp)[MATCH](String(O));
    },
    // `RegExp.prototype[@@match]` method
    // https://tc39.github.io/ecma262/#sec-regexp.prototype-@@match
    function (regexp) {
      var res = maybeCallNative($match, regexp, this);
      if (res.done) return res.value;
      var rx = anObject(regexp);
      var S = String(this);
      if (!rx.global) return regExpExec(rx, S);
      var fullUnicode = rx.unicode;
      rx.lastIndex = 0;
      var A = [];
      var n = 0;
      var result;
      while ((result = regExpExec(rx, S)) !== null) {
        var matchStr = String(result[0]);
        A[n] = matchStr;
        if (matchStr === '') rx.lastIndex = advanceStringIndex(S, toLength(rx.lastIndex), fullUnicode);
        n++;
      }
      return n === 0 ? null : A;
    }
  ];
});


/***/ }),

/***/ 4104:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.1.2.5 Number.isSafeInteger(number)
var $export = __webpack_require__(2127);
var isInteger = __webpack_require__(3842);
var abs = Math.abs;

$export($export.S, 'Number', {
  isSafeInteger: function isSafeInteger(number) {
    return isInteger(number) && abs(number) <= 0x1fffffffffffff;
  }
});


/***/ }),

/***/ 4116:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var regexpExec = __webpack_require__(9600);
__webpack_require__(2127)({
  target: 'RegExp',
  proto: true,
  forced: regexpExec !== /./.exec
}, {
  exec: regexpExec
});


/***/ }),

/***/ 4153:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// B.2.3.14 String.prototype.sup()
__webpack_require__(2468)('sup', function (createHTML) {
  return function sup() {
    return createHTML(this, 'sup', '', '');
  };
});


/***/ }),

/***/ 4228:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var isObject = __webpack_require__(3305);
module.exports = function (it) {
  if (!isObject(it)) throw TypeError(it + ' is not an object!');
  return it;
};


/***/ }),

/***/ 4258:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// 25.4.1.5 NewPromiseCapability(C)
var aFunction = __webpack_require__(3387);

function PromiseCapability(C) {
  var resolve, reject;
  this.promise = new C(function ($$resolve, $$reject) {
    if (resolve !== undefined || reject !== undefined) throw TypeError('Bad Promise constructor');
    resolve = $$resolve;
    reject = $$reject;
  });
  this.resolve = aFunction(resolve);
  this.reject = aFunction(reject);
}

module.exports.f = function (C) {
  return new PromiseCapability(C);
};


/***/ }),

/***/ 4352:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.2.2.17 Math.hypot([value1[, value2[, … ]]])
var $export = __webpack_require__(2127);
var abs = Math.abs;

$export($export.S, 'Math', {
  hypot: function hypot(value1, value2) { // eslint-disable-line no-unused-vars
    var sum = 0;
    var i = 0;
    var aLen = arguments.length;
    var larg = 0;
    var arg, div;
    while (i < aLen) {
      arg = abs(arguments[i++]);
      if (larg < arg) {
        div = larg / arg;
        sum = sum * div * div + 1;
        larg = arg;
      } else if (arg > 0) {
        div = arg / larg;
        sum += div * div;
      } else sum += arg;
    }
    return larg === Infinity ? Infinity : larg * Math.sqrt(sum);
  }
});


/***/ }),

/***/ 4376:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 26.1.4 Reflect.deleteProperty(target, propertyKey)
var $export = __webpack_require__(2127);
var gOPD = (__webpack_require__(8641).f);
var anObject = __webpack_require__(4228);

$export($export.S, 'Reflect', {
  deleteProperty: function deleteProperty(target, propertyKey) {
    var desc = gOPD(anObject(target), propertyKey);
    return desc && !desc.configurable ? false : delete target[propertyKey];
  }
});


/***/ }),

/***/ 4401:
/***/ (function(module) {

module.exports = function (it) {
  return typeof it === 'object' ? it !== null : typeof it === 'function';
};


/***/ }),

/***/ 4415:
/***/ (function(module) {

var id = 0;
var px = Math.random();
module.exports = function (key) {
  return 'Symbol('.concat(key === undefined ? '' : key, ')_', (++id + px).toString(36));
};


/***/ }),

/***/ 4419:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.1.2.1 Number.EPSILON
var $export = __webpack_require__(2127);

$export($export.S, 'Number', { EPSILON: Math.pow(2, -52) });


/***/ }),

/***/ 4437:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

var $export = __webpack_require__(2127);
var $parseFloat = __webpack_require__(3589);
// 20.1.2.12 Number.parseFloat(string)
$export($export.S + $export.F * (Number.parseFloat != $parseFloat), 'Number', { parseFloat: $parseFloat });


/***/ }),

/***/ 4438:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";
// 22.1.3.3 Array.prototype.copyWithin(target, start, end = this.length)

var toObject = __webpack_require__(8270);
var toAbsoluteIndex = __webpack_require__(157);
var toLength = __webpack_require__(1485);

module.exports = [].copyWithin || function copyWithin(target /* = 0 */, start /* = 0, end = @length */) {
  var O = toObject(this);
  var len = toLength(O.length);
  var to = toAbsoluteIndex(target, len);
  var from = toAbsoluteIndex(start, len);
  var end = arguments.length > 2 ? arguments[2] : undefined;
  var count = Math.min((end === undefined ? len : toAbsoluteIndex(end, len)) - from, len - to);
  var inc = 1;
  if (from < to && to < from + count) {
    inc = -1;
    from += count - 1;
    to += count - 1;
  }
  while (count-- > 0) {
    if (from in O) O[to] = O[from];
    else delete O[to];
    to += inc;
    from += inc;
  } return O;
};


/***/ }),

/***/ 4472:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// https://github.com/tc39/proposal-string-pad-start-end
var toLength = __webpack_require__(1485);
var repeat = __webpack_require__(7926);
var defined = __webpack_require__(3344);

module.exports = function (that, maxLength, fillString, left) {
  var S = String(defined(that));
  var stringLength = S.length;
  var fillStr = fillString === undefined ? ' ' : String(fillString);
  var intMaxLength = toLength(maxLength);
  if (intMaxLength <= stringLength || fillStr == '') return S;
  var fillLen = intMaxLength - stringLength;
  var stringFiller = repeat.call(fillStr, Math.ceil(fillLen / fillStr.length));
  if (stringFiller.length > fillLen) stringFiller = stringFiller.slice(0, fillLen);
  return left ? stringFiller + S : S + stringFiller;
};


/***/ }),

/***/ 4509:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var global = __webpack_require__(7526);
var has = __webpack_require__(7917);
var cof = __webpack_require__(5089);
var inheritIfRequired = __webpack_require__(8880);
var toPrimitive = __webpack_require__(3048);
var fails = __webpack_require__(9448);
var gOPN = (__webpack_require__(9415).f);
var gOPD = (__webpack_require__(8641).f);
var dP = (__webpack_require__(7967).f);
var $trim = (__webpack_require__(629).trim);
var NUMBER = 'Number';
var $Number = global[NUMBER];
var Base = $Number;
var proto = $Number.prototype;
// Opera ~12 has broken Object#toString
var BROKEN_COF = cof(__webpack_require__(4719)(proto)) == NUMBER;
var TRIM = 'trim' in String.prototype;

// 7.1.3 ToNumber(argument)
var toNumber = function (argument) {
  var it = toPrimitive(argument, false);
  if (typeof it == 'string' && it.length > 2) {
    it = TRIM ? it.trim() : $trim(it, 3);
    var first = it.charCodeAt(0);
    var third, radix, maxCode;
    if (first === 43 || first === 45) {
      third = it.charCodeAt(2);
      if (third === 88 || third === 120) return NaN; // Number('+0x1') should be NaN, old V8 fix
    } else if (first === 48) {
      switch (it.charCodeAt(1)) {
        case 66: case 98: radix = 2; maxCode = 49; break; // fast equal /^0b[01]+$/i
        case 79: case 111: radix = 8; maxCode = 55; break; // fast equal /^0o[0-7]+$/i
        default: return +it;
      }
      for (var digits = it.slice(2), i = 0, l = digits.length, code; i < l; i++) {
        code = digits.charCodeAt(i);
        // parseInt parses a string to a first unavailable symbol
        // but ToNumber should return NaN if a string contains unavailable symbols
        if (code < 48 || code > maxCode) return NaN;
      } return parseInt(digits, radix);
    }
  } return +it;
};

if (!$Number(' 0o1') || !$Number('0b1') || $Number('+0x1')) {
  $Number = function Number(value) {
    var it = arguments.length < 1 ? 0 : value;
    var that = this;
    return that instanceof $Number
      // check on 1..constructor(foo) case
      && (BROKEN_COF ? fails(function () { proto.valueOf.call(that); }) : cof(that) != NUMBER)
        ? inheritIfRequired(new Base(toNumber(it)), that, $Number) : toNumber(it);
  };
  for (var keys = __webpack_require__(1763) ? gOPN(Base) : (
    // ES3:
    'MAX_VALUE,MIN_VALUE,NaN,NEGATIVE_INFINITY,POSITIVE_INFINITY,' +
    // ES6 (in case, if modules with ES6 Number statics required before):
    'EPSILON,isFinite,isInteger,isNaN,isSafeInteger,MAX_SAFE_INTEGER,' +
    'MIN_SAFE_INTEGER,parseFloat,parseInt,isInteger'
  ).split(','), j = 0, key; keys.length > j; j++) {
    if (has(Base, key = keys[j]) && !has($Number, key)) {
      dP($Number, key, gOPD(Base, key));
    }
  }
  $Number.prototype = proto;
  proto.constructor = $Number;
  __webpack_require__(8859)(global, NUMBER, $Number);
}


/***/ }),

/***/ 4514:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var global = __webpack_require__(7526);
var navigator = global.navigator;

module.exports = navigator && navigator.userAgent || '';


/***/ }),

/***/ 4521:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.2.2.12 Math.cosh(x)
var $export = __webpack_require__(2127);
var exp = Math.exp;

$export($export.S, 'Math', {
  cosh: function cosh(x) {
    return (exp(x = +x) + exp(-x)) / 2;
  }
});


/***/ }),

/***/ 4556:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var core = __webpack_require__(6094);
var global = __webpack_require__(7526);
var SHARED = '__core-js_shared__';
var store = global[SHARED] || (global[SHARED] = {});

(module.exports = function (key, value) {
  return store[key] || (store[key] = value !== undefined ? value : {});
})('versions', []).push({
  version: core.version,
  mode: __webpack_require__(2750) ? 'pure' : 'global',
  copyright: '© 2020 Denis Pushkarev (zloirock.ru)'
});


/***/ }),

/***/ 4561:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var has = __webpack_require__(7917);
var toIObject = __webpack_require__(7221);
var arrayIndexOf = __webpack_require__(1464)(false);
var IE_PROTO = __webpack_require__(766)('IE_PROTO');

module.exports = function (object, names) {
  var O = toIObject(object);
  var i = 0;
  var result = [];
  var key;
  for (key in O) if (key != IE_PROTO) has(O, key) && result.push(key);
  // Don't enum bug & hidden keys
  while (names.length > i) if (has(O, key = names[i++])) {
    ~arrayIndexOf(result, key) || result.push(key);
  }
  return result;
};


/***/ }),

/***/ 4570:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var $export = __webpack_require__(2127);
var createProperty = __webpack_require__(7227);

// WebKit Array.of isn't generic
$export($export.S + $export.F * __webpack_require__(9448)(function () {
  function F() { /* empty */ }
  return !(Array.of.call(F) instanceof F);
}), 'Array', {
  // 22.1.2.3 Array.of( ...items)
  of: function of(/* ...args */) {
    var index = 0;
    var aLen = arguments.length;
    var result = new (typeof this == 'function' ? this : Array)(aLen);
    while (aLen > index) createProperty(result, index, arguments[index++]);
    result.length = aLen;
    return result;
  }
});


/***/ }),

/***/ 4572:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";


__webpack_require__(2419);

__webpack_require__(8128);

__webpack_require__(5777);

__webpack_require__(2681);

__webpack_require__(5240);

__webpack_require__(1368);

__webpack_require__(6073);

__webpack_require__(7739);

__webpack_require__(4897);

__webpack_require__(4925);

__webpack_require__(1243);

__webpack_require__(8978);

__webpack_require__(3415);

__webpack_require__(7452);

/***/ }),

/***/ 4591:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// B.2.3.4 String.prototype.blink()
__webpack_require__(2468)('blink', function (createHTML) {
  return function blink() {
    return createHTML(this, 'blink', '', '');
  };
});


/***/ }),

/***/ 4609:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var $export = __webpack_require__(2127);
var $indexOf = __webpack_require__(1464)(false);
var $native = [].indexOf;
var NEGATIVE_ZERO = !!$native && 1 / [1].indexOf(1, -0) < 0;

$export($export.P + $export.F * (NEGATIVE_ZERO || !__webpack_require__(6884)($native)), 'Array', {
  // 22.1.3.11 / 15.4.4.14 Array.prototype.indexOf(searchElement [, fromIndex])
  indexOf: function indexOf(searchElement /* , fromIndex = 0 */) {
    return NEGATIVE_ZERO
      // convert -0 to +0
      ? $native.apply(this, arguments) || 0
      : $indexOf(this, searchElement, arguments[1]);
  }
});


/***/ }),

/***/ 4614:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// https://github.com/tc39/proposal-object-getownpropertydescriptors
var $export = __webpack_require__(2127);
var ownKeys = __webpack_require__(6222);
var toIObject = __webpack_require__(7221);
var gOPD = __webpack_require__(8641);
var createProperty = __webpack_require__(7227);

$export($export.S, 'Object', {
  getOwnPropertyDescriptors: function getOwnPropertyDescriptors(object) {
    var O = toIObject(object);
    var getDesc = gOPD.f;
    var keys = ownKeys(O);
    var result = {};
    var i = 0;
    var key, desc;
    while (keys.length > i) {
      desc = getDesc(O, key = keys[i++]);
      if (desc !== undefined) createProperty(result, key, desc);
    }
    return result;
  }
});


/***/ }),

/***/ 4701:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var anObject = __webpack_require__(4228);
var sameValue = __webpack_require__(7359);
var regExpExec = __webpack_require__(2535);

// @@search logic
__webpack_require__(9228)('search', 1, function (defined, SEARCH, $search, maybeCallNative) {
  return [
    // `String.prototype.search` method
    // https://tc39.github.io/ecma262/#sec-string.prototype.search
    function search(regexp) {
      var O = defined(this);
      var fn = regexp == undefined ? undefined : regexp[SEARCH];
      return fn !== undefined ? fn.call(regexp, O) : new RegExp(regexp)[SEARCH](String(O));
    },
    // `RegExp.prototype[@@search]` method
    // https://tc39.github.io/ecma262/#sec-regexp.prototype-@@search
    function (regexp) {
      var res = maybeCallNative($search, regexp, this);
      if (res.done) return res.value;
      var rx = anObject(regexp);
      var S = String(this);
      var previousLastIndex = rx.lastIndex;
      if (!sameValue(previousLastIndex, 0)) rx.lastIndex = 0;
      var result = regExpExec(rx, S);
      if (!sameValue(rx.lastIndex, previousLastIndex)) rx.lastIndex = previousLastIndex;
      return result === null ? -1 : result.index;
    }
  ];
});


/***/ }),

/***/ 4702:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(7209)('Uint8', 1, function (init) {
  return function Uint8Array(data, byteOffset, length) {
    return init(this, data, byteOffset, length);
  };
});


/***/ }),

/***/ 4719:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// 19.1.2.2 / 15.2.3.5 Object.create(O [, Properties])
var anObject = __webpack_require__(4228);
var dPs = __webpack_require__(1626);
var enumBugKeys = __webpack_require__(6140);
var IE_PROTO = __webpack_require__(766)('IE_PROTO');
var Empty = function () { /* empty */ };
var PROTOTYPE = 'prototype';

// Create object with fake `null` prototype: use iframe Object with cleared prototype
var createDict = function () {
  // Thrash, waste and sodomy: IE GC bug
  var iframe = __webpack_require__(6034)('iframe');
  var i = enumBugKeys.length;
  var lt = '<';
  var gt = '>';
  var iframeDocument;
  iframe.style.display = 'none';
  (__webpack_require__(1308).appendChild)(iframe);
  iframe.src = 'javascript:'; // eslint-disable-line no-script-url
  // createDict = iframe.contentWindow.Object;
  // html.removeChild(iframe);
  iframeDocument = iframe.contentWindow.document;
  iframeDocument.open();
  iframeDocument.write(lt + 'script' + gt + 'document.F=Object' + lt + '/script' + gt);
  iframeDocument.close();
  createDict = iframeDocument.F;
  while (i--) delete createDict[PROTOTYPE][enumBugKeys[i]];
  return createDict();
};

module.exports = Object.create || function create(O, Properties) {
  var result;
  if (O !== null) {
    Empty[PROTOTYPE] = anObject(O);
    result = new Empty();
    Empty[PROTOTYPE] = null;
    // add "__proto__" for Object.getPrototypeOf polyfill
    result[IE_PROTO] = O;
  } else result = createDict();
  return Properties === undefined ? result : dPs(result, Properties);
};


/***/ }),

/***/ 4765:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// fallback for IE11 buggy Object.getOwnPropertyNames with iframe and window
var toIObject = __webpack_require__(7221);
var gOPN = (__webpack_require__(9415).f);
var toString = {}.toString;

var windowNames = typeof window == 'object' && window && Object.getOwnPropertyNames
  ? Object.getOwnPropertyNames(window) : [];

var getWindowNames = function (it) {
  try {
    return gOPN(it);
  } catch (e) {
    return windowNames.slice();
  }
};

module.exports.f = function getOwnPropertyNames(it) {
  return windowNames && toString.call(it) == '[object Window]' ? getWindowNames(it) : gOPN(toIObject(it));
};


/***/ }),

/***/ 4814:
/***/ (function() {

const accordions = document.querySelectorAll('.su-accordion');
const titleButtons = document.querySelectorAll('.su-accordion__button');
const expandButtons = document.querySelectorAll('.su-accordion__expand-all');
const collapseButtons = document.querySelectorAll('.su-accordion__collapse-all');

const isExpanded = x => x.getAttribute('aria-expanded') === 'true';
const setExpanded = (x, value) => x.setAttribute('aria-expanded', value);
const setHidden = (x, value) => x.setAttribute('aria-hidden', value);

document.addEventListener('DOMContentLoaded', event => {
  Array.prototype.forEach.call(accordions, acc => {
    acc.classList.remove('no-js');
  });

  Array.prototype.forEach.call(titleButtons, btn => {
    setExpanded(btn, 'false');
    setHidden(btn.parentNode.nextElementSibling, 'true');
  });
});

Array.prototype.forEach.call(titleButtons, btn => {
  btn.addEventListener('click', function (e) {
    if (!isExpanded(btn)) {
      setExpanded(btn, 'true');
      setHidden(btn.parentNode.nextElementSibling, 'false');
    }
    else {
      setExpanded(btn, 'false');
      setHidden(btn.parentNode.nextElementSibling, 'true');
    }
  }, false);
});

Array.prototype.forEach.call(expandButtons, expandBtn => {
  expandBtn.addEventListener('click', function (e) {
    const closestAccordion = expandBtn.closest('.su-accordion');
    const closestBtns = closestAccordion.querySelectorAll('.su-accordion__button');
    Array.prototype.forEach.call(closestBtns, closestBtn => {
      setExpanded(closestBtn, 'true');
      setHidden(closestBtn.parentNode.nextElementSibling, 'false');
    });
  }, false);
});

Array.prototype.forEach.call(collapseButtons, collapseBtn => {
  collapseBtn.addEventListener('click', function (e) {
    const closestAccordion = collapseBtn.closest('.su-accordion');
    const closestBtns = closestAccordion.querySelectorAll('.su-accordion__button');
    Array.prototype.forEach.call(closestBtns, closestBtn => {
      setExpanded(closestBtn, 'false');
      setHidden(closestBtn.parentNode.nextElementSibling, 'true');
    });
  }, false);
});


/***/ }),

/***/ 4848:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// getting tag from 19.1.3.6 Object.prototype.toString()
var cof = __webpack_require__(5089);
var TAG = __webpack_require__(7574)('toStringTag');
// ES3 wrong here
var ARG = cof(function () { return arguments; }()) == 'Arguments';

// fallback for IE11 Script Access Denied error
var tryGet = function (it, key) {
  try {
    return it[key];
  } catch (e) { /* empty */ }
};

module.exports = function (it) {
  var O, T, B;
  return it === undefined ? 'Undefined' : it === null ? 'Null'
    // @@toStringTag case
    : typeof (T = tryGet(O = Object(it), TAG)) == 'string' ? T
    // builtinTag case
    : ARG ? cof(O)
    // ES3 arguments fallback
    : (B = cof(O)) == 'Object' && typeof O.callee == 'function' ? 'Arguments' : B;
};


/***/ }),

/***/ 4877:
/***/ (function(module) {

// fast apply, http://jsperf.lnkit.com/fast-apply/5
module.exports = function (fn, args, that) {
  var un = that === undefined;
  switch (args.length) {
    case 0: return un ? fn()
                      : fn.call(that);
    case 1: return un ? fn(args[0])
                      : fn.call(that, args[0]);
    case 2: return un ? fn(args[0], args[1])
                      : fn.call(that, args[0], args[1]);
    case 3: return un ? fn(args[0], args[1], args[2])
                      : fn.call(that, args[0], args[1], args[2]);
    case 4: return un ? fn(args[0], args[1], args[2], args[3])
                      : fn.call(that, args[0], args[1], args[2], args[3]);
  } return fn.apply(that, args);
};


/***/ }),

/***/ 4894:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

var $export = __webpack_require__(2127);

$export($export.P, 'String', {
  // 21.1.3.13 String.prototype.repeat(count)
  repeat: __webpack_require__(7926)
});


/***/ }),

/***/ 4896:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.2.2.11 Math.clz32(x)
var $export = __webpack_require__(2127);

$export($export.S, 'Math', {
  clz32: function clz32(x) {
    return (x >>>= 0) ? 31 - Math.floor(Math.log(x + 0.5) * Math.LOG2E) : 32;
  }
});


/***/ }),

/***/ 4897:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(4614);
module.exports = __webpack_require__(6094).Object.getOwnPropertyDescriptors;


/***/ }),

/***/ 4913:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var $export = __webpack_require__(2127);
var $map = __webpack_require__(6179)(1);

$export($export.P + $export.F * !__webpack_require__(6884)([].map, true), 'Array', {
  // 22.1.3.15 / 15.4.4.19 Array.prototype.map(callbackfn [, thisArg])
  map: function map(callbackfn /* , thisArg */) {
    return $map(this, callbackfn, arguments[1]);
  }
});


/***/ }),

/***/ 4925:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(7594);
module.exports = __webpack_require__(6094).Object.values;


/***/ }),

/***/ 4970:
/***/ (function(module) {

module.exports = function (done, value) {
  return { value: value, done: !!done };
};


/***/ }),

/***/ 5000:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 26.1.11 Reflect.ownKeys(target)
var $export = __webpack_require__(2127);

$export($export.S, 'Reflect', { ownKeys: __webpack_require__(6222) });


/***/ }),

/***/ 5032:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 19.1.2.13 Object.isSealed(O)
var isObject = __webpack_require__(3305);

__webpack_require__(923)('isSealed', function ($isSealed) {
  return function isSealed(it) {
    return isObject(it) ? $isSealed ? $isSealed(it) : false : true;
  };
});


/***/ }),

/***/ 5049:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 19.2.3.2 / 15.3.4.5 Function.prototype.bind(thisArg, args...)
var $export = __webpack_require__(2127);

$export($export.P, 'Function', { bind: __webpack_require__(5538) });


/***/ }),

/***/ 5052:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// optional / simple context binding
var aFunction = __webpack_require__(3387);
module.exports = function (fn, that, length) {
  aFunction(fn);
  if (that === undefined) return fn;
  switch (length) {
    case 1: return function (a) {
      return fn.call(that, a);
    };
    case 2: return function (a, b) {
      return fn.call(that, a, b);
    };
    case 3: return function (a, b, c) {
      return fn.call(that, a, b, c);
    };
  }
  return function (/* ...args */) {
    return fn.apply(that, arguments);
  };
};


/***/ }),

/***/ 5089:
/***/ (function(module) {

var toString = {}.toString;

module.exports = function (it) {
  return toString.call(it).slice(8, -1);
};


/***/ }),

/***/ 5104:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(1124);
module.exports = __webpack_require__(6438).global;


/***/ }),

/***/ 5122:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var cof = __webpack_require__(5089);
module.exports = function (it, msg) {
  if (typeof it != 'number' && cof(it) != 'Number') throw TypeError(msg);
  return +it;
};


/***/ }),

/***/ 5144:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// 22.1.3.8 Array.prototype.find(predicate, thisArg = undefined)
var $export = __webpack_require__(2127);
var $find = __webpack_require__(6179)(5);
var KEY = 'find';
var forced = true;
// Shouldn't skip holes
if (KEY in []) Array(1)[KEY](function () { forced = false; });
$export($export.P + $export.F * forced, 'Array', {
  find: function find(callbackfn /* , that = undefined */) {
    return $find(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
  }
});
__webpack_require__(8184)(KEY);


/***/ }),

/***/ 5153:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// 26.1.5 Reflect.enumerate(target)
var $export = __webpack_require__(2127);
var anObject = __webpack_require__(4228);
var Enumerate = function (iterated) {
  this._t = anObject(iterated); // target
  this._i = 0;                  // next index
  var keys = this._k = [];      // keys
  var key;
  for (key in iterated) keys.push(key);
};
__webpack_require__(6032)(Enumerate, 'Object', function () {
  var that = this;
  var keys = that._k;
  var key;
  do {
    if (that._i >= keys.length) return { value: undefined, done: true };
  } while (!((key = keys[that._i++]) in that._t));
  return { value: key, done: false };
});

$export($export.S, 'Reflect', {
  enumerate: function enumerate(target) {
    return new Enumerate(target);
  }
});


/***/ }),

/***/ 5165:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var addToUnscopables = __webpack_require__(8184);
var step = __webpack_require__(4970);
var Iterators = __webpack_require__(906);
var toIObject = __webpack_require__(7221);

// 22.1.3.4 Array.prototype.entries()
// 22.1.3.13 Array.prototype.keys()
// 22.1.3.29 Array.prototype.values()
// 22.1.3.30 Array.prototype[@@iterator]()
module.exports = __webpack_require__(8175)(Array, 'Array', function (iterated, kind) {
  this._t = toIObject(iterated); // target
  this._i = 0;                   // next index
  this._k = kind;                // kind
// 22.1.5.2.1 %ArrayIteratorPrototype%.next()
}, function () {
  var O = this._t;
  var kind = this._k;
  var index = this._i++;
  if (!O || index >= O.length) {
    this._t = undefined;
    return step(1);
  }
  if (kind == 'keys') return step(0, index);
  if (kind == 'values') return step(0, O[index]);
  return step(0, [index, O[index]]);
}, 'values');

// argumentsList[@@iterator] is %ArrayProto_values% (9.4.4.6, 9.4.4.7)
Iterators.Arguments = Iterators.Array;

addToUnscopables('keys');
addToUnscopables('values');
addToUnscopables('entries');


/***/ }),

/***/ 5170:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// Works with __proto__ only. Old v8 can't work with null proto objects.
/* eslint-disable no-proto */
var isObject = __webpack_require__(3305);
var anObject = __webpack_require__(4228);
var check = function (O, proto) {
  anObject(O);
  if (!isObject(proto) && proto !== null) throw TypeError(proto + ": can't set as prototype!");
};
module.exports = {
  set: Object.setPrototypeOf || ('__proto__' in {} ? // eslint-disable-line
    function (test, buggy, set) {
      try {
        set = __webpack_require__(5052)(Function.call, (__webpack_require__(8641).f)(Object.prototype, '__proto__').set, 2);
        set(test, []);
        buggy = !(test instanceof Array);
      } catch (e) { buggy = true; }
      return function setPrototypeOf(O, proto) {
        check(O, proto);
        if (buggy) O.__proto__ = proto;
        else set(O, proto);
        return O;
      };
    }({}, false) : undefined),
  check: check
};


/***/ }),

/***/ 5203:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var MATCH = __webpack_require__(7574)('match');
module.exports = function (KEY) {
  var re = /./;
  try {
    '/./'[KEY](re);
  } catch (e) {
    try {
      re[MATCH] = false;
      return !'/./'[KEY](re);
    } catch (f) { /* empty */ }
  } return true;
};


/***/ }),

/***/ 5219:
/***/ (function(module) {

module.exports = function (it) {
  if (typeof it != 'function') throw TypeError(it + ' is not a function!');
  return it;
};


/***/ }),

/***/ 5240:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(5693);
module.exports = __webpack_require__(6094).String.padEnd;


/***/ }),

/***/ 5327:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.2.2.18 Math.imul(x, y)
var $export = __webpack_require__(2127);
var $imul = Math.imul;

// some WebKit versions fails with big numbers, some has wrong arity
$export($export.S + $export.F * __webpack_require__(9448)(function () {
  return $imul(0xffffffff, 5) != -5 || $imul.length != 2;
}), 'Math', {
  imul: function imul(x, y) {
    var UINT16 = 0xffff;
    var xn = +x;
    var yn = +y;
    var xl = UINT16 & xn;
    var yl = UINT16 & yn;
    return 0 | xl * yl + ((UINT16 & xn >>> 16) * yl + xl * (UINT16 & yn >>> 16) << 16 >>> 0);
  }
});


/***/ }),

/***/ 5369:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// 22.1.3.9 Array.prototype.findIndex(predicate, thisArg = undefined)
var $export = __webpack_require__(2127);
var $find = __webpack_require__(6179)(6);
var KEY = 'findIndex';
var forced = true;
// Shouldn't skip holes
if (KEY in []) Array(1)[KEY](function () { forced = false; });
$export($export.P + $export.F * forced, 'Array', {
  findIndex: function findIndex(callbackfn /* , that = undefined */) {
    return $find(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
  }
});
__webpack_require__(8184)(KEY);


/***/ }),

/***/ 5380:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// https://github.com/tc39/proposal-string-pad-start-end
var $export = __webpack_require__(2127);
var $pad = __webpack_require__(4472);
var userAgent = __webpack_require__(4514);

// https://github.com/zloirock/core-js/issues/280
var WEBKIT_BUG = /Version\/10\.\d+(\.\d+)?( Mobile\/\w+)? Safari\//.test(userAgent);

$export($export.P + $export.F * WEBKIT_BUG, 'String', {
  padStart: function padStart(maxLength /* , fillString = ' ' */) {
    return $pad(this, maxLength, arguments.length > 1 ? arguments[1] : undefined, true);
  }
});


/***/ }),

/***/ 5385:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// 20.3.4.36 / 15.9.5.43 Date.prototype.toISOString()
var fails = __webpack_require__(9448);
var getTime = Date.prototype.getTime;
var $toISOString = Date.prototype.toISOString;

var lz = function (num) {
  return num > 9 ? num : '0' + num;
};

// PhantomJS / old WebKit has a broken implementations
module.exports = (fails(function () {
  return $toISOString.call(new Date(-5e13 - 1)) != '0385-07-25T07:06:39.999Z';
}) || !fails(function () {
  $toISOString.call(new Date(NaN));
})) ? function toISOString() {
  if (!isFinite(getTime.call(this))) throw RangeError('Invalid time value');
  var d = this;
  var y = d.getUTCFullYear();
  var m = d.getUTCMilliseconds();
  var s = y < 0 ? '-' : y > 9999 ? '+' : '';
  return s + ('00000' + Math.abs(y)).slice(s ? -6 : -4) +
    '-' + lz(d.getUTCMonth() + 1) + '-' + lz(d.getUTCDate()) +
    'T' + lz(d.getUTCHours()) + ':' + lz(d.getUTCMinutes()) +
    ':' + lz(d.getUTCSeconds()) + '.' + (m > 99 ? m : '0' + lz(m)) + 'Z';
} : $toISOString;


/***/ }),

/***/ 5392:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var global = __webpack_require__(7526);
var core = __webpack_require__(6094);
var LIBRARY = __webpack_require__(2750);
var wksExt = __webpack_require__(7960);
var defineProperty = (__webpack_require__(7967).f);
module.exports = function (name) {
  var $Symbol = core.Symbol || (core.Symbol = LIBRARY ? {} : global.Symbol || {});
  if (name.charAt(0) != '_' && !(name in $Symbol)) defineProperty($Symbol, name, { value: wksExt.f(name) });
};


/***/ }),

/***/ 5411:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// 7.2.8 IsRegExp(argument)
var isObject = __webpack_require__(3305);
var cof = __webpack_require__(5089);
var MATCH = __webpack_require__(7574)('match');
module.exports = function (it) {
  var isRegExp;
  return isObject(it) && ((isRegExp = it[MATCH]) !== undefined ? !!isRegExp : cof(it) == 'RegExp');
};


/***/ }),

/***/ 5417:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

var $export = __webpack_require__(2127);
var $task = __webpack_require__(2780);
$export($export.G + $export.B, {
  setImmediate: $task.set,
  clearImmediate: $task.clear
});


/***/ }),

/***/ 5433:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 26.1.10 Reflect.isExtensible(target)
var $export = __webpack_require__(2127);
var anObject = __webpack_require__(4228);
var $isExtensible = Object.isExtensible;

$export($export.S, 'Reflect', {
  isExtensible: function isExtensible(target) {
    anObject(target);
    return $isExtensible ? $isExtensible(target) : true;
  }
});


/***/ }),

/***/ 5443:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 26.1.13 Reflect.set(target, propertyKey, V [, receiver])
var dP = __webpack_require__(7967);
var gOPD = __webpack_require__(8641);
var getPrototypeOf = __webpack_require__(627);
var has = __webpack_require__(7917);
var $export = __webpack_require__(2127);
var createDesc = __webpack_require__(1996);
var anObject = __webpack_require__(4228);
var isObject = __webpack_require__(3305);

function set(target, propertyKey, V /* , receiver */) {
  var receiver = arguments.length < 4 ? target : arguments[3];
  var ownDesc = gOPD.f(anObject(target), propertyKey);
  var existingDescriptor, proto;
  if (!ownDesc) {
    if (isObject(proto = getPrototypeOf(target))) {
      return set(proto, propertyKey, V, receiver);
    }
    ownDesc = createDesc(0);
  }
  if (has(ownDesc, 'value')) {
    if (ownDesc.writable === false || !isObject(receiver)) return false;
    if (existingDescriptor = gOPD.f(receiver, propertyKey)) {
      if (existingDescriptor.get || existingDescriptor.set || existingDescriptor.writable === false) return false;
      existingDescriptor.value = V;
      dP.f(receiver, propertyKey, existingDescriptor);
    } else dP.f(receiver, propertyKey, createDesc(0, V));
    return true;
  }
  return ownDesc.set === undefined ? false : (ownDesc.set.call(receiver, V), true);
}

$export($export.S, 'Reflect', { set: set });


/***/ }),

/***/ 5502:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var isObject = __webpack_require__(3305);
var getPrototypeOf = __webpack_require__(627);
var HAS_INSTANCE = __webpack_require__(7574)('hasInstance');
var FunctionProto = Function.prototype;
// 19.2.3.6 Function.prototype[@@hasInstance](V)
if (!(HAS_INSTANCE in FunctionProto)) (__webpack_require__(7967).f)(FunctionProto, HAS_INSTANCE, { value: function (O) {
  if (typeof this != 'function' || !isObject(O)) return false;
  if (!isObject(this.prototype)) return O instanceof this;
  // for environment w/o native `@@hasInstance` logic enough `instanceof`, but add this:
  while (O = getPrototypeOf(O)) if (this.prototype === O) return true;
  return false;
} });


/***/ }),

/***/ 5509:
/***/ (function(module) {

var hasOwnProperty = {}.hasOwnProperty;
module.exports = function (it, key) {
  return hasOwnProperty.call(it, key);
};


/***/ }),

/***/ 5538:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var aFunction = __webpack_require__(3387);
var isObject = __webpack_require__(3305);
var invoke = __webpack_require__(4877);
var arraySlice = [].slice;
var factories = {};

var construct = function (F, len, args) {
  if (!(len in factories)) {
    for (var n = [], i = 0; i < len; i++) n[i] = 'a[' + i + ']';
    // eslint-disable-next-line no-new-func
    factories[len] = Function('F,a', 'return new F(' + n.join(',') + ')');
  } return factories[len](F, args);
};

module.exports = Function.bind || function bind(that /* , ...args */) {
  var fn = aFunction(this);
  var partArgs = arraySlice.call(arguments, 1);
  var bound = function (/* args... */) {
    var args = partArgs.concat(arraySlice.call(arguments));
    return this instanceof bound ? construct(fn, args.length, args) : invoke(fn, args, that);
  };
  if (isObject(fn.prototype)) bound.prototype = fn.prototype;
  return bound;
};


/***/ }),

/***/ 5551:
/***/ (function(module) {

// 20.2.2.14 Math.expm1(x)
var $expm1 = Math.expm1;
module.exports = (!$expm1
  // Old FF bug
  || $expm1(10) > 22025.465794806719 || $expm1(10) < 22025.4657948067165168
  // Tor Browser bug
  || $expm1(-2e-17) != -2e-17
) ? function expm1(x) {
  return (x = +x) == 0 ? x : x > -1e-6 && x < 1e-6 ? x + x * x / 2 : Math.exp(x) - 1;
} : $expm1;


/***/ }),

/***/ 5564:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";
// 22.1.3.6 Array.prototype.fill(value, start = 0, end = this.length)

var toObject = __webpack_require__(8270);
var toAbsoluteIndex = __webpack_require__(157);
var toLength = __webpack_require__(1485);
module.exports = function fill(value /* , start = 0, end = @length */) {
  var O = toObject(this);
  var length = toLength(O.length);
  var aLen = arguments.length;
  var index = toAbsoluteIndex(aLen > 1 ? arguments[1] : undefined, length);
  var end = aLen > 2 ? arguments[2] : undefined;
  var endPos = end === undefined ? length : toAbsoluteIndex(end, length);
  while (endPos > index) O[index++] = value;
  return O;
};


/***/ }),

/***/ 5572:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 19.1.2.15 Object.preventExtensions(O)
var isObject = __webpack_require__(3305);
var meta = (__webpack_require__(2988).onFreeze);

__webpack_require__(923)('preventExtensions', function ($preventExtensions) {
  return function preventExtensions(it) {
    return $preventExtensions && isObject(it) ? $preventExtensions(meta(it)) : it;
  };
});


/***/ }),

/***/ 5693:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// https://github.com/tc39/proposal-string-pad-start-end
var $export = __webpack_require__(2127);
var $pad = __webpack_require__(4472);
var userAgent = __webpack_require__(4514);

// https://github.com/zloirock/core-js/issues/280
var WEBKIT_BUG = /Version\/10\.\d+(\.\d+)?( Mobile\/\w+)? Safari\//.test(userAgent);

$export($export.P + $export.F * WEBKIT_BUG, 'String', {
  padEnd: function padEnd(maxLength /* , fillString = ' ' */) {
    return $pad(this, maxLength, arguments.length > 1 ? arguments[1] : undefined, false);
  }
});


/***/ }),

/***/ 5706:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var $export = __webpack_require__(2127);
var $typed = __webpack_require__(237);
var buffer = __webpack_require__(8032);
var anObject = __webpack_require__(4228);
var toAbsoluteIndex = __webpack_require__(157);
var toLength = __webpack_require__(1485);
var isObject = __webpack_require__(3305);
var ArrayBuffer = (__webpack_require__(7526).ArrayBuffer);
var speciesConstructor = __webpack_require__(9190);
var $ArrayBuffer = buffer.ArrayBuffer;
var $DataView = buffer.DataView;
var $isView = $typed.ABV && ArrayBuffer.isView;
var $slice = $ArrayBuffer.prototype.slice;
var VIEW = $typed.VIEW;
var ARRAY_BUFFER = 'ArrayBuffer';

$export($export.G + $export.W + $export.F * (ArrayBuffer !== $ArrayBuffer), { ArrayBuffer: $ArrayBuffer });

$export($export.S + $export.F * !$typed.CONSTR, ARRAY_BUFFER, {
  // 24.1.3.1 ArrayBuffer.isView(arg)
  isView: function isView(it) {
    return $isView && $isView(it) || isObject(it) && VIEW in it;
  }
});

$export($export.P + $export.U + $export.F * __webpack_require__(9448)(function () {
  return !new $ArrayBuffer(2).slice(1, undefined).byteLength;
}), ARRAY_BUFFER, {
  // 24.1.4.3 ArrayBuffer.prototype.slice(start, end)
  slice: function slice(start, end) {
    if ($slice !== undefined && end === undefined) return $slice.call(anObject(this), start); // FF fix
    var len = anObject(this).byteLength;
    var first = toAbsoluteIndex(start, len);
    var fin = toAbsoluteIndex(end === undefined ? len : end, len);
    var result = new (speciesConstructor(this, $ArrayBuffer))(toLength(fin - first));
    var viewS = new $DataView(this);
    var viewT = new $DataView(result);
    var index = 0;
    while (first < fin) {
      viewT.setUint8(index++, viewS.getUint8(first++));
    } return result;
  }
});

__webpack_require__(5762)(ARRAY_BUFFER);


/***/ }),

/***/ 5762:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var global = __webpack_require__(7526);
var dP = __webpack_require__(7967);
var DESCRIPTORS = __webpack_require__(1763);
var SPECIES = __webpack_require__(7574)('species');

module.exports = function (KEY) {
  var C = global[KEY];
  if (DESCRIPTORS && C && !C[SPECIES]) dP.f(C, SPECIES, {
    configurable: true,
    get: function () { return this; }
  });
};


/***/ }),

/***/ 5771:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.2.2.5 Math.asinh(x)
var $export = __webpack_require__(2127);
var $asinh = Math.asinh;

function asinh(x) {
  return !isFinite(x = +x) || x == 0 ? x : x < 0 ? -asinh(-x) : Math.log(x + Math.sqrt(x * x + 1));
}

// Tor Browser bug: Math.asinh(0) -> -0
$export($export.S + $export.F * !($asinh && 1 / $asinh(0) > 0), 'Math', { asinh: asinh });


/***/ }),

/***/ 5777:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(9766);
module.exports = __webpack_require__(6094).Array.flatMap;


/***/ }),

/***/ 5853:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var $export = __webpack_require__(2127);
var html = __webpack_require__(1308);
var cof = __webpack_require__(5089);
var toAbsoluteIndex = __webpack_require__(157);
var toLength = __webpack_require__(1485);
var arraySlice = [].slice;

// fallback for not array-like ES3 strings and DOM objects
$export($export.P + $export.F * __webpack_require__(9448)(function () {
  if (html) arraySlice.call(html);
}), 'Array', {
  slice: function slice(begin, end) {
    var len = toLength(this.length);
    var klass = cof(this);
    end = end === undefined ? len : end;
    if (klass == 'Array') return arraySlice.call(this, begin, end);
    var start = toAbsoluteIndex(begin, len);
    var upTo = toAbsoluteIndex(end, len);
    var size = toLength(upTo - start);
    var cloned = new Array(size);
    var i = 0;
    for (; i < size; i++) cloned[i] = klass == 'String'
      ? this.charAt(start + i)
      : this[start + i];
    return cloned;
  }
});


/***/ }),

/***/ 5890:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

var $iterators = __webpack_require__(5165);
var getKeys = __webpack_require__(1311);
var redefine = __webpack_require__(8859);
var global = __webpack_require__(7526);
var hide = __webpack_require__(3341);
var Iterators = __webpack_require__(906);
var wks = __webpack_require__(7574);
var ITERATOR = wks('iterator');
var TO_STRING_TAG = wks('toStringTag');
var ArrayValues = Iterators.Array;

var DOMIterables = {
  CSSRuleList: true, // TODO: Not spec compliant, should be false.
  CSSStyleDeclaration: false,
  CSSValueList: false,
  ClientRectList: false,
  DOMRectList: false,
  DOMStringList: false,
  DOMTokenList: true,
  DataTransferItemList: false,
  FileList: false,
  HTMLAllCollection: false,
  HTMLCollection: false,
  HTMLFormElement: false,
  HTMLSelectElement: false,
  MediaList: true, // TODO: Not spec compliant, should be false.
  MimeTypeArray: false,
  NamedNodeMap: false,
  NodeList: true,
  PaintRequestList: false,
  Plugin: false,
  PluginArray: false,
  SVGLengthList: false,
  SVGNumberList: false,
  SVGPathSegList: false,
  SVGPointList: false,
  SVGStringList: false,
  SVGTransformList: false,
  SourceBufferList: false,
  StyleSheetList: true, // TODO: Not spec compliant, should be false.
  TextTrackCueList: false,
  TextTrackList: false,
  TouchList: false
};

for (var collections = getKeys(DOMIterables), i = 0; i < collections.length; i++) {
  var NAME = collections[i];
  var explicit = DOMIterables[NAME];
  var Collection = global[NAME];
  var proto = Collection && Collection.prototype;
  var key;
  if (proto) {
    if (!proto[ITERATOR]) hide(proto, ITERATOR, ArrayValues);
    if (!proto[TO_STRING_TAG]) hide(proto, TO_STRING_TAG, NAME);
    Iterators[NAME] = ArrayValues;
    if (explicit) for (key in $iterators) if (!proto[key]) redefine(proto, key, $iterators[key], true);
  }
}


/***/ }),

/***/ 5909:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.2.2.20 Math.log1p(x)
var $export = __webpack_require__(2127);

$export($export.S, 'Math', { log1p: __webpack_require__(1473) });


/***/ }),

/***/ 5932:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 26.1.12 Reflect.preventExtensions(target)
var $export = __webpack_require__(2127);
var anObject = __webpack_require__(4228);
var $preventExtensions = Object.preventExtensions;

$export($export.S, 'Reflect', {
  preventExtensions: function preventExtensions(target) {
    anObject(target);
    try {
      if ($preventExtensions) $preventExtensions(target);
      return true;
    } catch (e) {
      return false;
    }
  }
});


/***/ }),

/***/ 5957:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var anObject = __webpack_require__(4228);
var isObject = __webpack_require__(3305);
var newPromiseCapability = __webpack_require__(4258);

module.exports = function (C, x) {
  anObject(C);
  if (isObject(x) && x.constructor === C) return x;
  var promiseCapability = newPromiseCapability.f(C);
  var resolve = promiseCapability.resolve;
  resolve(x);
  return promiseCapability.promise;
};


/***/ }),

/***/ 5969:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// all enumerable object keys, includes symbols
var getKeys = __webpack_require__(1311);
var gOPS = __webpack_require__(1060);
var pIE = __webpack_require__(8449);
module.exports = function (it) {
  var result = getKeys(it);
  var getSymbols = gOPS.f;
  if (getSymbols) {
    var symbols = getSymbols(it);
    var isEnum = pIE.f;
    var i = 0;
    var key;
    while (symbols.length > i) if (isEnum.call(it, key = symbols[i++])) result.push(key);
  } return result;
};


/***/ }),

/***/ 6032:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var create = __webpack_require__(4719);
var descriptor = __webpack_require__(1996);
var setToStringTag = __webpack_require__(3844);
var IteratorPrototype = {};

// 25.1.2.1.1 %IteratorPrototype%[@@iterator]()
__webpack_require__(3341)(IteratorPrototype, __webpack_require__(7574)('iterator'), function () { return this; });

module.exports = function (Constructor, NAME, next) {
  Constructor.prototype = create(IteratorPrototype, { next: descriptor(1, next) });
  setToStringTag(Constructor, NAME + ' Iterator');
};


/***/ }),

/***/ 6034:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var isObject = __webpack_require__(3305);
var document = (__webpack_require__(7526).document);
// typeof document.createElement is 'object' in old IE
var is = isObject(document) && isObject(document.createElement);
module.exports = function (it) {
  return is ? document.createElement(it) : {};
};


/***/ }),

/***/ 6064:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

var $export = __webpack_require__(2127);
// 19.1.2.4 / 15.2.3.6 Object.defineProperty(O, P, Attributes)
$export($export.S + $export.F * !__webpack_require__(1763), 'Object', { defineProperty: (__webpack_require__(7967).f) });


/***/ }),

/***/ 6065:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var redefine = __webpack_require__(8859);
module.exports = function (target, src, safe) {
  for (var key in src) redefine(target, key, src[key], safe);
  return target;
};


/***/ }),

/***/ 6073:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(521);
module.exports = __webpack_require__(6094).String.trimRight;


/***/ }),

/***/ 6094:
/***/ (function(module) {

var core = module.exports = { version: '2.6.12' };
if (typeof __e == 'number') __e = core; // eslint-disable-line no-undef


/***/ }),

/***/ 6103:
/***/ (function() {

if (typeof Element !== "undefined") {
    if (!Element.prototype.matches) {
        Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
    }

    if (!Element.prototype.closest) {
        Element.prototype.closest = function (s) {
            var el = this;

            do {
                if (el.matches(s)) return el;
                el = el.parentElement || el.parentNode;
            } while (el !== null && el.nodeType === 1);
            
            return null;
        };
    }
}


/***/ }),

/***/ 6108:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

var $export = __webpack_require__(2127);
var $parseFloat = __webpack_require__(3589);
// 18.2.4 parseFloat(string)
$export($export.G + $export.F * (parseFloat != $parseFloat), { parseFloat: $parseFloat });


/***/ }),

/***/ 6140:
/***/ (function(module) {

// IE 8- don't enum bug keys
module.exports = (
  'constructor,hasOwnProperty,isPrototypeOf,propertyIsEnumerable,toLocaleString,toString,valueOf'
).split(',');


/***/ }),

/***/ 6179:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// 0 -> Array#forEach
// 1 -> Array#map
// 2 -> Array#filter
// 3 -> Array#some
// 4 -> Array#every
// 5 -> Array#find
// 6 -> Array#findIndex
var ctx = __webpack_require__(5052);
var IObject = __webpack_require__(1249);
var toObject = __webpack_require__(8270);
var toLength = __webpack_require__(1485);
var asc = __webpack_require__(3191);
module.exports = function (TYPE, $create) {
  var IS_MAP = TYPE == 1;
  var IS_FILTER = TYPE == 2;
  var IS_SOME = TYPE == 3;
  var IS_EVERY = TYPE == 4;
  var IS_FIND_INDEX = TYPE == 6;
  var NO_HOLES = TYPE == 5 || IS_FIND_INDEX;
  var create = $create || asc;
  return function ($this, callbackfn, that) {
    var O = toObject($this);
    var self = IObject(O);
    var f = ctx(callbackfn, that, 3);
    var length = toLength(self.length);
    var index = 0;
    var result = IS_MAP ? create($this, length) : IS_FILTER ? create($this, 0) : undefined;
    var val, res;
    for (;length > index; index++) if (NO_HOLES || index in self) {
      val = self[index];
      res = f(val, index, O);
      if (TYPE) {
        if (IS_MAP) result[index] = res;   // map
        else if (res) switch (TYPE) {
          case 3: return true;             // some
          case 5: return val;              // find
          case 6: return index;            // findIndex
          case 2: result.push(val);        // filter
        } else if (IS_EVERY) return false; // every
      }
    }
    return IS_FIND_INDEX ? -1 : IS_SOME || IS_EVERY ? IS_EVERY : result;
  };
};


/***/ }),

/***/ 6197:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var dP = (__webpack_require__(7967).f);
var create = __webpack_require__(4719);
var redefineAll = __webpack_require__(6065);
var ctx = __webpack_require__(5052);
var anInstance = __webpack_require__(6440);
var forOf = __webpack_require__(8790);
var $iterDefine = __webpack_require__(8175);
var step = __webpack_require__(4970);
var setSpecies = __webpack_require__(5762);
var DESCRIPTORS = __webpack_require__(1763);
var fastKey = (__webpack_require__(2988).fastKey);
var validate = __webpack_require__(2888);
var SIZE = DESCRIPTORS ? '_s' : 'size';

var getEntry = function (that, key) {
  // fast case
  var index = fastKey(key);
  var entry;
  if (index !== 'F') return that._i[index];
  // frozen object case
  for (entry = that._f; entry; entry = entry.n) {
    if (entry.k == key) return entry;
  }
};

module.exports = {
  getConstructor: function (wrapper, NAME, IS_MAP, ADDER) {
    var C = wrapper(function (that, iterable) {
      anInstance(that, C, NAME, '_i');
      that._t = NAME;         // collection type
      that._i = create(null); // index
      that._f = undefined;    // first entry
      that._l = undefined;    // last entry
      that[SIZE] = 0;         // size
      if (iterable != undefined) forOf(iterable, IS_MAP, that[ADDER], that);
    });
    redefineAll(C.prototype, {
      // 23.1.3.1 Map.prototype.clear()
      // 23.2.3.2 Set.prototype.clear()
      clear: function clear() {
        for (var that = validate(this, NAME), data = that._i, entry = that._f; entry; entry = entry.n) {
          entry.r = true;
          if (entry.p) entry.p = entry.p.n = undefined;
          delete data[entry.i];
        }
        that._f = that._l = undefined;
        that[SIZE] = 0;
      },
      // 23.1.3.3 Map.prototype.delete(key)
      // 23.2.3.4 Set.prototype.delete(value)
      'delete': function (key) {
        var that = validate(this, NAME);
        var entry = getEntry(that, key);
        if (entry) {
          var next = entry.n;
          var prev = entry.p;
          delete that._i[entry.i];
          entry.r = true;
          if (prev) prev.n = next;
          if (next) next.p = prev;
          if (that._f == entry) that._f = next;
          if (that._l == entry) that._l = prev;
          that[SIZE]--;
        } return !!entry;
      },
      // 23.2.3.6 Set.prototype.forEach(callbackfn, thisArg = undefined)
      // 23.1.3.5 Map.prototype.forEach(callbackfn, thisArg = undefined)
      forEach: function forEach(callbackfn /* , that = undefined */) {
        validate(this, NAME);
        var f = ctx(callbackfn, arguments.length > 1 ? arguments[1] : undefined, 3);
        var entry;
        while (entry = entry ? entry.n : this._f) {
          f(entry.v, entry.k, this);
          // revert to the last existing entry
          while (entry && entry.r) entry = entry.p;
        }
      },
      // 23.1.3.7 Map.prototype.has(key)
      // 23.2.3.7 Set.prototype.has(value)
      has: function has(key) {
        return !!getEntry(validate(this, NAME), key);
      }
    });
    if (DESCRIPTORS) dP(C.prototype, 'size', {
      get: function () {
        return validate(this, NAME)[SIZE];
      }
    });
    return C;
  },
  def: function (that, key, value) {
    var entry = getEntry(that, key);
    var prev, index;
    // change existing entry
    if (entry) {
      entry.v = value;
    // create new entry
    } else {
      that._l = entry = {
        i: index = fastKey(key, true), // <- index
        k: key,                        // <- key
        v: value,                      // <- value
        p: prev = that._l,             // <- previous entry
        n: undefined,                  // <- next entry
        r: false                       // <- removed
      };
      if (!that._f) that._f = entry;
      if (prev) prev.n = entry;
      that[SIZE]++;
      // add to index
      if (index !== 'F') that._i[index] = entry;
    } return that;
  },
  getEntry: getEntry,
  setStrong: function (C, NAME, IS_MAP) {
    // add .keys, .values, .entries, [@@iterator]
    // 23.1.3.4, 23.1.3.8, 23.1.3.11, 23.1.3.12, 23.2.3.5, 23.2.3.8, 23.2.3.10, 23.2.3.11
    $iterDefine(C, NAME, function (iterated, kind) {
      this._t = validate(iterated, NAME); // target
      this._k = kind;                     // kind
      this._l = undefined;                // previous
    }, function () {
      var that = this;
      var kind = that._k;
      var entry = that._l;
      // revert to the last existing entry
      while (entry && entry.r) entry = entry.p;
      // get next entry
      if (!that._t || !(that._l = entry = entry ? entry.n : that._t._f)) {
        // or finish the iteration
        that._t = undefined;
        return step(1);
      }
      // return step by kind
      if (kind == 'keys') return step(0, entry.k);
      if (kind == 'values') return step(0, entry.v);
      return step(0, [entry.k, entry.v]);
    }, IS_MAP ? 'entries' : 'values', !IS_MAP, true);

    // add [@@species], 23.1.2.2, 23.2.2.2
    setSpecies(NAME);
  }
};


/***/ }),

/***/ 6209:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(5762)('Array');


/***/ }),

/***/ 6222:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// all object keys, includes non-enumerable and symbols
var gOPN = __webpack_require__(9415);
var gOPS = __webpack_require__(1060);
var anObject = __webpack_require__(4228);
var Reflect = (__webpack_require__(7526).Reflect);
module.exports = Reflect && Reflect.ownKeys || function ownKeys(it) {
  var keys = gOPN.f(anObject(it));
  var getSymbols = gOPS.f;
  return getSymbols ? keys.concat(getSymbols(it)) : keys;
};


/***/ }),

/***/ 6260:
/***/ (function(module) {

module.exports = function (bitmap, value) {
  return {
    enumerable: !(bitmap & 1),
    configurable: !(bitmap & 2),
    writable: !(bitmap & 4),
    value: value
  };
};


/***/ }),

/***/ 6316:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 26.1.14 Reflect.setPrototypeOf(target, proto)
var $export = __webpack_require__(2127);
var setProto = __webpack_require__(5170);

if (setProto) $export($export.S, 'Reflect', {
  setPrototypeOf: function setPrototypeOf(target, proto) {
    setProto.check(target, proto);
    try {
      setProto.set(target, proto);
      return true;
    } catch (e) {
      return false;
    }
  }
});


/***/ }),

/***/ 6438:
/***/ (function(module) {

var core = module.exports = { version: '2.6.12' };
if (typeof __e == 'number') __e = core; // eslint-disable-line no-undef


/***/ }),

/***/ 6440:
/***/ (function(module) {

module.exports = function (it, Constructor, name, forbiddenField) {
  if (!(it instanceof Constructor) || (forbiddenField !== undefined && forbiddenField in it)) {
    throw TypeError(name + ': incorrect invocation!');
  } return it;
};


/***/ }),

/***/ 6497:
/***/ (function() {

if (!Element.prototype.matches) {
  Element.prototype.matches =
    Element.prototype.matchesSelector ||
    Element.prototype.mozMatchesSelector ||
    Element.prototype.msMatchesSelector ||
    Element.prototype.oMatchesSelector ||
    Element.prototype.webkitMatchesSelector ||
    function(s) {
      var matches = (this.document || this.ownerDocument).querySelectorAll(s),
        i = matches.length;
      while (--i >= 0 && matches.item(i) !== this) {}
      return i > -1;
    };
}


/***/ }),

/***/ 6511:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// 22.1.3.13 Array.prototype.join(separator)
var $export = __webpack_require__(2127);
var toIObject = __webpack_require__(7221);
var arrayJoin = [].join;

// fallback for not array-like strings
$export($export.P + $export.F * (__webpack_require__(1249) != Object || !__webpack_require__(6884)(arrayJoin)), 'Array', {
  join: function join(separator) {
    return arrayJoin.call(toIObject(this), separator === undefined ? ',' : separator);
  }
});


/***/ }),

/***/ 6517:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var LIBRARY = __webpack_require__(2750);
var global = __webpack_require__(7526);
var ctx = __webpack_require__(5052);
var classof = __webpack_require__(4848);
var $export = __webpack_require__(2127);
var isObject = __webpack_require__(3305);
var aFunction = __webpack_require__(3387);
var anInstance = __webpack_require__(6440);
var forOf = __webpack_require__(8790);
var speciesConstructor = __webpack_require__(9190);
var task = (__webpack_require__(2780).set);
var microtask = __webpack_require__(1384)();
var newPromiseCapabilityModule = __webpack_require__(4258);
var perform = __webpack_require__(128);
var userAgent = __webpack_require__(4514);
var promiseResolve = __webpack_require__(5957);
var PROMISE = 'Promise';
var TypeError = global.TypeError;
var process = global.process;
var versions = process && process.versions;
var v8 = versions && versions.v8 || '';
var $Promise = global[PROMISE];
var isNode = classof(process) == 'process';
var empty = function () { /* empty */ };
var Internal, newGenericPromiseCapability, OwnPromiseCapability, Wrapper;
var newPromiseCapability = newGenericPromiseCapability = newPromiseCapabilityModule.f;

var USE_NATIVE = !!function () {
  try {
    // correct subclassing with @@species support
    var promise = $Promise.resolve(1);
    var FakePromise = (promise.constructor = {})[__webpack_require__(7574)('species')] = function (exec) {
      exec(empty, empty);
    };
    // unhandled rejections tracking support, NodeJS Promise without it fails @@species test
    return (isNode || typeof PromiseRejectionEvent == 'function')
      && promise.then(empty) instanceof FakePromise
      // v8 6.6 (Node 10 and Chrome 66) have a bug with resolving custom thenables
      // https://bugs.chromium.org/p/chromium/issues/detail?id=830565
      // we can't detect it synchronously, so just check versions
      && v8.indexOf('6.6') !== 0
      && userAgent.indexOf('Chrome/66') === -1;
  } catch (e) { /* empty */ }
}();

// helpers
var isThenable = function (it) {
  var then;
  return isObject(it) && typeof (then = it.then) == 'function' ? then : false;
};
var notify = function (promise, isReject) {
  if (promise._n) return;
  promise._n = true;
  var chain = promise._c;
  microtask(function () {
    var value = promise._v;
    var ok = promise._s == 1;
    var i = 0;
    var run = function (reaction) {
      var handler = ok ? reaction.ok : reaction.fail;
      var resolve = reaction.resolve;
      var reject = reaction.reject;
      var domain = reaction.domain;
      var result, then, exited;
      try {
        if (handler) {
          if (!ok) {
            if (promise._h == 2) onHandleUnhandled(promise);
            promise._h = 1;
          }
          if (handler === true) result = value;
          else {
            if (domain) domain.enter();
            result = handler(value); // may throw
            if (domain) {
              domain.exit();
              exited = true;
            }
          }
          if (result === reaction.promise) {
            reject(TypeError('Promise-chain cycle'));
          } else if (then = isThenable(result)) {
            then.call(result, resolve, reject);
          } else resolve(result);
        } else reject(value);
      } catch (e) {
        if (domain && !exited) domain.exit();
        reject(e);
      }
    };
    while (chain.length > i) run(chain[i++]); // variable length - can't use forEach
    promise._c = [];
    promise._n = false;
    if (isReject && !promise._h) onUnhandled(promise);
  });
};
var onUnhandled = function (promise) {
  task.call(global, function () {
    var value = promise._v;
    var unhandled = isUnhandled(promise);
    var result, handler, console;
    if (unhandled) {
      result = perform(function () {
        if (isNode) {
          process.emit('unhandledRejection', value, promise);
        } else if (handler = global.onunhandledrejection) {
          handler({ promise: promise, reason: value });
        } else if ((console = global.console) && console.error) {
          console.error('Unhandled promise rejection', value);
        }
      });
      // Browsers should not trigger `rejectionHandled` event if it was handled here, NodeJS - should
      promise._h = isNode || isUnhandled(promise) ? 2 : 1;
    } promise._a = undefined;
    if (unhandled && result.e) throw result.v;
  });
};
var isUnhandled = function (promise) {
  return promise._h !== 1 && (promise._a || promise._c).length === 0;
};
var onHandleUnhandled = function (promise) {
  task.call(global, function () {
    var handler;
    if (isNode) {
      process.emit('rejectionHandled', promise);
    } else if (handler = global.onrejectionhandled) {
      handler({ promise: promise, reason: promise._v });
    }
  });
};
var $reject = function (value) {
  var promise = this;
  if (promise._d) return;
  promise._d = true;
  promise = promise._w || promise; // unwrap
  promise._v = value;
  promise._s = 2;
  if (!promise._a) promise._a = promise._c.slice();
  notify(promise, true);
};
var $resolve = function (value) {
  var promise = this;
  var then;
  if (promise._d) return;
  promise._d = true;
  promise = promise._w || promise; // unwrap
  try {
    if (promise === value) throw TypeError("Promise can't be resolved itself");
    if (then = isThenable(value)) {
      microtask(function () {
        var wrapper = { _w: promise, _d: false }; // wrap
        try {
          then.call(value, ctx($resolve, wrapper, 1), ctx($reject, wrapper, 1));
        } catch (e) {
          $reject.call(wrapper, e);
        }
      });
    } else {
      promise._v = value;
      promise._s = 1;
      notify(promise, false);
    }
  } catch (e) {
    $reject.call({ _w: promise, _d: false }, e); // wrap
  }
};

// constructor polyfill
if (!USE_NATIVE) {
  // 25.4.3.1 Promise(executor)
  $Promise = function Promise(executor) {
    anInstance(this, $Promise, PROMISE, '_h');
    aFunction(executor);
    Internal.call(this);
    try {
      executor(ctx($resolve, this, 1), ctx($reject, this, 1));
    } catch (err) {
      $reject.call(this, err);
    }
  };
  // eslint-disable-next-line no-unused-vars
  Internal = function Promise(executor) {
    this._c = [];             // <- awaiting reactions
    this._a = undefined;      // <- checked in isUnhandled reactions
    this._s = 0;              // <- state
    this._d = false;          // <- done
    this._v = undefined;      // <- value
    this._h = 0;              // <- rejection state, 0 - default, 1 - handled, 2 - unhandled
    this._n = false;          // <- notify
  };
  Internal.prototype = __webpack_require__(6065)($Promise.prototype, {
    // 25.4.5.3 Promise.prototype.then(onFulfilled, onRejected)
    then: function then(onFulfilled, onRejected) {
      var reaction = newPromiseCapability(speciesConstructor(this, $Promise));
      reaction.ok = typeof onFulfilled == 'function' ? onFulfilled : true;
      reaction.fail = typeof onRejected == 'function' && onRejected;
      reaction.domain = isNode ? process.domain : undefined;
      this._c.push(reaction);
      if (this._a) this._a.push(reaction);
      if (this._s) notify(this, false);
      return reaction.promise;
    },
    // 25.4.5.1 Promise.prototype.catch(onRejected)
    'catch': function (onRejected) {
      return this.then(undefined, onRejected);
    }
  });
  OwnPromiseCapability = function () {
    var promise = new Internal();
    this.promise = promise;
    this.resolve = ctx($resolve, promise, 1);
    this.reject = ctx($reject, promise, 1);
  };
  newPromiseCapabilityModule.f = newPromiseCapability = function (C) {
    return C === $Promise || C === Wrapper
      ? new OwnPromiseCapability(C)
      : newGenericPromiseCapability(C);
  };
}

$export($export.G + $export.W + $export.F * !USE_NATIVE, { Promise: $Promise });
__webpack_require__(3844)($Promise, PROMISE);
__webpack_require__(5762)(PROMISE);
Wrapper = __webpack_require__(6094)[PROMISE];

// statics
$export($export.S + $export.F * !USE_NATIVE, PROMISE, {
  // 25.4.4.5 Promise.reject(r)
  reject: function reject(r) {
    var capability = newPromiseCapability(this);
    var $$reject = capability.reject;
    $$reject(r);
    return capability.promise;
  }
});
$export($export.S + $export.F * (LIBRARY || !USE_NATIVE), PROMISE, {
  // 25.4.4.6 Promise.resolve(x)
  resolve: function resolve(x) {
    return promiseResolve(LIBRARY && this === Wrapper ? $Promise : this, x);
  }
});
$export($export.S + $export.F * !(USE_NATIVE && __webpack_require__(8931)(function (iter) {
  $Promise.all(iter)['catch'](empty);
})), PROMISE, {
  // 25.4.4.1 Promise.all(iterable)
  all: function all(iterable) {
    var C = this;
    var capability = newPromiseCapability(C);
    var resolve = capability.resolve;
    var reject = capability.reject;
    var result = perform(function () {
      var values = [];
      var index = 0;
      var remaining = 1;
      forOf(iterable, false, function (promise) {
        var $index = index++;
        var alreadyCalled = false;
        values.push(undefined);
        remaining++;
        C.resolve(promise).then(function (value) {
          if (alreadyCalled) return;
          alreadyCalled = true;
          values[$index] = value;
          --remaining || resolve(values);
        }, reject);
      });
      --remaining || resolve(values);
    });
    if (result.e) reject(result.v);
    return capability.promise;
  },
  // 25.4.4.4 Promise.race(iterable)
  race: function race(iterable) {
    var C = this;
    var capability = newPromiseCapability(C);
    var reject = capability.reject;
    var result = perform(function () {
      forOf(iterable, false, function (promise) {
        C.resolve(promise).then(capability.resolve, reject);
      });
    });
    if (result.e) reject(result.v);
    return capability.promise;
  }
});


/***/ }),

/***/ 6543:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var aFunction = __webpack_require__(3387);
var toObject = __webpack_require__(8270);
var IObject = __webpack_require__(1249);
var toLength = __webpack_require__(1485);

module.exports = function (that, callbackfn, aLen, memo, isRight) {
  aFunction(callbackfn);
  var O = toObject(that);
  var self = IObject(O);
  var length = toLength(O.length);
  var index = isRight ? length - 1 : 0;
  var i = isRight ? -1 : 1;
  if (aLen < 2) for (;;) {
    if (index in self) {
      memo = self[index];
      index += i;
      break;
    }
    index += i;
    if (isRight ? index < 0 : length <= index) {
      throw TypeError('Reduce of empty array with no initial value');
    }
  }
  for (;isRight ? index >= 0 : length > index; index += i) if (index in self) {
    memo = callbackfn(memo, self[index], index, O);
  }
  return memo;
};


/***/ }),

/***/ 6549:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// B.2.3.10 String.prototype.link(url)
__webpack_require__(2468)('link', function (createHTML) {
  return function link(url) {
    return createHTML(this, 'a', 'href', url);
  };
});


/***/ }),

/***/ 6576:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.1.2.10 Number.MIN_SAFE_INTEGER
var $export = __webpack_require__(2127);

$export($export.S, 'Number', { MIN_SAFE_INTEGER: -0x1fffffffffffff });


/***/ }),

/***/ 6592:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.2.2.34 Math.trunc(x)
var $export = __webpack_require__(2127);

$export($export.S, 'Math', {
  trunc: function trunc(it) {
    return (it > 0 ? Math.floor : Math.ceil)(it);
  }
});


/***/ }),

/***/ 6648:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.2.2.3 Math.acosh(x)
var $export = __webpack_require__(2127);
var log1p = __webpack_require__(1473);
var sqrt = Math.sqrt;
var $acosh = Math.acosh;

$export($export.S + $export.F * !($acosh
  // V8 bug: https://code.google.com/p/v8/issues/detail?id=3509
  && Math.floor($acosh(Number.MAX_VALUE)) == 710
  // Tor Browser bug: Math.acosh(Infinity) -> NaN
  && $acosh(Infinity) == Infinity
), 'Math', {
  acosh: function acosh(x) {
    return (x = +x) < 1 ? NaN : x > 94906265.62425156
      ? Math.log(x) + Math.LN2
      : log1p(x - 1 + sqrt(x - 1) * sqrt(x + 1));
  }
});


/***/ }),

/***/ 6670:
/***/ (function(module) {

// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
var global = module.exports = typeof window != 'undefined' && window.Math == Math
  ? window : typeof self != 'undefined' && self.Math == Math ? self
  // eslint-disable-next-line no-new-func
  : Function('return this')();
if (typeof __g == 'number') __g = global; // eslint-disable-line no-undef


/***/ }),

/***/ 6701:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var $export = __webpack_require__(2127);
var $fails = __webpack_require__(9448);
var aNumberValue = __webpack_require__(5122);
var $toPrecision = 1.0.toPrecision;

$export($export.P + $export.F * ($fails(function () {
  // IE7-
  return $toPrecision.call(1, undefined) !== '1';
}) || !$fails(function () {
  // V8 ~ Android 4.3-
  $toPrecision.call({});
})), 'Number', {
  toPrecision: function toPrecision(precision) {
    var that = aNumberValue(this, 'Number#toPrecision: incorrect invocation!');
    return precision === undefined ? $toPrecision.call(that) : $toPrecision.call(that, precision);
  }
});


/***/ }),

/***/ 6884:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var fails = __webpack_require__(9448);

module.exports = function (method, arg) {
  return !!method && fails(function () {
    // eslint-disable-next-line no-useless-call
    arg ? method.call(null, function () { /* empty */ }, 1) : method.call(null);
  });
};


/***/ }),

/***/ 7067:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

var $export = __webpack_require__(2127);
// 19.1.2.3 / 15.2.3.7 Object.defineProperties(O, Properties)
$export($export.S + $export.F * !__webpack_require__(1763), 'Object', { defineProperties: __webpack_require__(1626) });


/***/ }),

/***/ 7075:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var $export = __webpack_require__(2127);
var aFunction = __webpack_require__(3387);
var toObject = __webpack_require__(8270);
var fails = __webpack_require__(9448);
var $sort = [].sort;
var test = [1, 2, 3];

$export($export.P + $export.F * (fails(function () {
  // IE8-
  test.sort(undefined);
}) || !fails(function () {
  // V8 bug
  test.sort(null);
  // Old WebKit
}) || !__webpack_require__(6884)($sort)), 'Array', {
  // 22.1.3.25 Array.prototype.sort(comparefn)
  sort: function sort(comparefn) {
    return comparefn === undefined
      ? $sort.call(toObject(this))
      : $sort.call(toObject(this), aFunction(comparefn));
  }
});


/***/ }),

/***/ 7083:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// B.2.3.6 String.prototype.fixed()
__webpack_require__(2468)('fixed', function (createHTML) {
  return function fixed() {
    return createHTML(this, 'tt', '', '');
  };
});


/***/ }),

/***/ 7087:
/***/ (function(module) {

// 7.1.4 ToInteger
var ceil = Math.ceil;
var floor = Math.floor;
module.exports = function (it) {
  return isNaN(it = +it) ? 0 : (it > 0 ? floor : ceil)(it);
};


/***/ }),

/***/ 7103:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 26.1.1 Reflect.apply(target, thisArgument, argumentsList)
var $export = __webpack_require__(2127);
var aFunction = __webpack_require__(3387);
var anObject = __webpack_require__(4228);
var rApply = ((__webpack_require__(7526).Reflect) || {}).apply;
var fApply = Function.apply;
// MS Edge argumentsList argument is optional
$export($export.S + $export.F * !__webpack_require__(9448)(function () {
  rApply(function () { /* empty */ });
}), 'Reflect', {
  apply: function apply(target, thisArgument, argumentsList) {
    var T = aFunction(target);
    var L = anObject(argumentsList);
    return rApply ? rApply(T, thisArgument, L) : fApply.call(T, thisArgument, L);
  }
});


/***/ }),

/***/ 7146:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// https://github.com/tc39/proposal-object-values-entries
var $export = __webpack_require__(2127);
var $entries = __webpack_require__(3854)(true);

$export($export.S, 'Object', {
  entries: function entries(it) {
    return $entries(it);
  }
});


/***/ }),

/***/ 7209:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

if (__webpack_require__(1763)) {
  var LIBRARY = __webpack_require__(2750);
  var global = __webpack_require__(7526);
  var fails = __webpack_require__(9448);
  var $export = __webpack_require__(2127);
  var $typed = __webpack_require__(237);
  var $buffer = __webpack_require__(8032);
  var ctx = __webpack_require__(5052);
  var anInstance = __webpack_require__(6440);
  var propertyDesc = __webpack_require__(1996);
  var hide = __webpack_require__(3341);
  var redefineAll = __webpack_require__(6065);
  var toInteger = __webpack_require__(7087);
  var toLength = __webpack_require__(1485);
  var toIndex = __webpack_require__(3133);
  var toAbsoluteIndex = __webpack_require__(157);
  var toPrimitive = __webpack_require__(3048);
  var has = __webpack_require__(7917);
  var classof = __webpack_require__(4848);
  var isObject = __webpack_require__(3305);
  var toObject = __webpack_require__(8270);
  var isArrayIter = __webpack_require__(1508);
  var create = __webpack_require__(4719);
  var getPrototypeOf = __webpack_require__(627);
  var gOPN = (__webpack_require__(9415).f);
  var getIterFn = __webpack_require__(762);
  var uid = __webpack_require__(4415);
  var wks = __webpack_require__(7574);
  var createArrayMethod = __webpack_require__(6179);
  var createArrayIncludes = __webpack_require__(1464);
  var speciesConstructor = __webpack_require__(9190);
  var ArrayIterators = __webpack_require__(5165);
  var Iterators = __webpack_require__(906);
  var $iterDetect = __webpack_require__(8931);
  var setSpecies = __webpack_require__(5762);
  var arrayFill = __webpack_require__(5564);
  var arrayCopyWithin = __webpack_require__(4438);
  var $DP = __webpack_require__(7967);
  var $GOPD = __webpack_require__(8641);
  var dP = $DP.f;
  var gOPD = $GOPD.f;
  var RangeError = global.RangeError;
  var TypeError = global.TypeError;
  var Uint8Array = global.Uint8Array;
  var ARRAY_BUFFER = 'ArrayBuffer';
  var SHARED_BUFFER = 'Shared' + ARRAY_BUFFER;
  var BYTES_PER_ELEMENT = 'BYTES_PER_ELEMENT';
  var PROTOTYPE = 'prototype';
  var ArrayProto = Array[PROTOTYPE];
  var $ArrayBuffer = $buffer.ArrayBuffer;
  var $DataView = $buffer.DataView;
  var arrayForEach = createArrayMethod(0);
  var arrayFilter = createArrayMethod(2);
  var arraySome = createArrayMethod(3);
  var arrayEvery = createArrayMethod(4);
  var arrayFind = createArrayMethod(5);
  var arrayFindIndex = createArrayMethod(6);
  var arrayIncludes = createArrayIncludes(true);
  var arrayIndexOf = createArrayIncludes(false);
  var arrayValues = ArrayIterators.values;
  var arrayKeys = ArrayIterators.keys;
  var arrayEntries = ArrayIterators.entries;
  var arrayLastIndexOf = ArrayProto.lastIndexOf;
  var arrayReduce = ArrayProto.reduce;
  var arrayReduceRight = ArrayProto.reduceRight;
  var arrayJoin = ArrayProto.join;
  var arraySort = ArrayProto.sort;
  var arraySlice = ArrayProto.slice;
  var arrayToString = ArrayProto.toString;
  var arrayToLocaleString = ArrayProto.toLocaleString;
  var ITERATOR = wks('iterator');
  var TAG = wks('toStringTag');
  var TYPED_CONSTRUCTOR = uid('typed_constructor');
  var DEF_CONSTRUCTOR = uid('def_constructor');
  var ALL_CONSTRUCTORS = $typed.CONSTR;
  var TYPED_ARRAY = $typed.TYPED;
  var VIEW = $typed.VIEW;
  var WRONG_LENGTH = 'Wrong length!';

  var $map = createArrayMethod(1, function (O, length) {
    return allocate(speciesConstructor(O, O[DEF_CONSTRUCTOR]), length);
  });

  var LITTLE_ENDIAN = fails(function () {
    // eslint-disable-next-line no-undef
    return new Uint8Array(new Uint16Array([1]).buffer)[0] === 1;
  });

  var FORCED_SET = !!Uint8Array && !!Uint8Array[PROTOTYPE].set && fails(function () {
    new Uint8Array(1).set({});
  });

  var toOffset = function (it, BYTES) {
    var offset = toInteger(it);
    if (offset < 0 || offset % BYTES) throw RangeError('Wrong offset!');
    return offset;
  };

  var validate = function (it) {
    if (isObject(it) && TYPED_ARRAY in it) return it;
    throw TypeError(it + ' is not a typed array!');
  };

  var allocate = function (C, length) {
    if (!(isObject(C) && TYPED_CONSTRUCTOR in C)) {
      throw TypeError('It is not a typed array constructor!');
    } return new C(length);
  };

  var speciesFromList = function (O, list) {
    return fromList(speciesConstructor(O, O[DEF_CONSTRUCTOR]), list);
  };

  var fromList = function (C, list) {
    var index = 0;
    var length = list.length;
    var result = allocate(C, length);
    while (length > index) result[index] = list[index++];
    return result;
  };

  var addGetter = function (it, key, internal) {
    dP(it, key, { get: function () { return this._d[internal]; } });
  };

  var $from = function from(source /* , mapfn, thisArg */) {
    var O = toObject(source);
    var aLen = arguments.length;
    var mapfn = aLen > 1 ? arguments[1] : undefined;
    var mapping = mapfn !== undefined;
    var iterFn = getIterFn(O);
    var i, length, values, result, step, iterator;
    if (iterFn != undefined && !isArrayIter(iterFn)) {
      for (iterator = iterFn.call(O), values = [], i = 0; !(step = iterator.next()).done; i++) {
        values.push(step.value);
      } O = values;
    }
    if (mapping && aLen > 2) mapfn = ctx(mapfn, arguments[2], 2);
    for (i = 0, length = toLength(O.length), result = allocate(this, length); length > i; i++) {
      result[i] = mapping ? mapfn(O[i], i) : O[i];
    }
    return result;
  };

  var $of = function of(/* ...items */) {
    var index = 0;
    var length = arguments.length;
    var result = allocate(this, length);
    while (length > index) result[index] = arguments[index++];
    return result;
  };

  // iOS Safari 6.x fails here
  var TO_LOCALE_BUG = !!Uint8Array && fails(function () { arrayToLocaleString.call(new Uint8Array(1)); });

  var $toLocaleString = function toLocaleString() {
    return arrayToLocaleString.apply(TO_LOCALE_BUG ? arraySlice.call(validate(this)) : validate(this), arguments);
  };

  var proto = {
    copyWithin: function copyWithin(target, start /* , end */) {
      return arrayCopyWithin.call(validate(this), target, start, arguments.length > 2 ? arguments[2] : undefined);
    },
    every: function every(callbackfn /* , thisArg */) {
      return arrayEvery(validate(this), callbackfn, arguments.length > 1 ? arguments[1] : undefined);
    },
    fill: function fill(value /* , start, end */) { // eslint-disable-line no-unused-vars
      return arrayFill.apply(validate(this), arguments);
    },
    filter: function filter(callbackfn /* , thisArg */) {
      return speciesFromList(this, arrayFilter(validate(this), callbackfn,
        arguments.length > 1 ? arguments[1] : undefined));
    },
    find: function find(predicate /* , thisArg */) {
      return arrayFind(validate(this), predicate, arguments.length > 1 ? arguments[1] : undefined);
    },
    findIndex: function findIndex(predicate /* , thisArg */) {
      return arrayFindIndex(validate(this), predicate, arguments.length > 1 ? arguments[1] : undefined);
    },
    forEach: function forEach(callbackfn /* , thisArg */) {
      arrayForEach(validate(this), callbackfn, arguments.length > 1 ? arguments[1] : undefined);
    },
    indexOf: function indexOf(searchElement /* , fromIndex */) {
      return arrayIndexOf(validate(this), searchElement, arguments.length > 1 ? arguments[1] : undefined);
    },
    includes: function includes(searchElement /* , fromIndex */) {
      return arrayIncludes(validate(this), searchElement, arguments.length > 1 ? arguments[1] : undefined);
    },
    join: function join(separator) { // eslint-disable-line no-unused-vars
      return arrayJoin.apply(validate(this), arguments);
    },
    lastIndexOf: function lastIndexOf(searchElement /* , fromIndex */) { // eslint-disable-line no-unused-vars
      return arrayLastIndexOf.apply(validate(this), arguments);
    },
    map: function map(mapfn /* , thisArg */) {
      return $map(validate(this), mapfn, arguments.length > 1 ? arguments[1] : undefined);
    },
    reduce: function reduce(callbackfn /* , initialValue */) { // eslint-disable-line no-unused-vars
      return arrayReduce.apply(validate(this), arguments);
    },
    reduceRight: function reduceRight(callbackfn /* , initialValue */) { // eslint-disable-line no-unused-vars
      return arrayReduceRight.apply(validate(this), arguments);
    },
    reverse: function reverse() {
      var that = this;
      var length = validate(that).length;
      var middle = Math.floor(length / 2);
      var index = 0;
      var value;
      while (index < middle) {
        value = that[index];
        that[index++] = that[--length];
        that[length] = value;
      } return that;
    },
    some: function some(callbackfn /* , thisArg */) {
      return arraySome(validate(this), callbackfn, arguments.length > 1 ? arguments[1] : undefined);
    },
    sort: function sort(comparefn) {
      return arraySort.call(validate(this), comparefn);
    },
    subarray: function subarray(begin, end) {
      var O = validate(this);
      var length = O.length;
      var $begin = toAbsoluteIndex(begin, length);
      return new (speciesConstructor(O, O[DEF_CONSTRUCTOR]))(
        O.buffer,
        O.byteOffset + $begin * O.BYTES_PER_ELEMENT,
        toLength((end === undefined ? length : toAbsoluteIndex(end, length)) - $begin)
      );
    }
  };

  var $slice = function slice(start, end) {
    return speciesFromList(this, arraySlice.call(validate(this), start, end));
  };

  var $set = function set(arrayLike /* , offset */) {
    validate(this);
    var offset = toOffset(arguments[1], 1);
    var length = this.length;
    var src = toObject(arrayLike);
    var len = toLength(src.length);
    var index = 0;
    if (len + offset > length) throw RangeError(WRONG_LENGTH);
    while (index < len) this[offset + index] = src[index++];
  };

  var $iterators = {
    entries: function entries() {
      return arrayEntries.call(validate(this));
    },
    keys: function keys() {
      return arrayKeys.call(validate(this));
    },
    values: function values() {
      return arrayValues.call(validate(this));
    }
  };

  var isTAIndex = function (target, key) {
    return isObject(target)
      && target[TYPED_ARRAY]
      && typeof key != 'symbol'
      && key in target
      && String(+key) == String(key);
  };
  var $getDesc = function getOwnPropertyDescriptor(target, key) {
    return isTAIndex(target, key = toPrimitive(key, true))
      ? propertyDesc(2, target[key])
      : gOPD(target, key);
  };
  var $setDesc = function defineProperty(target, key, desc) {
    if (isTAIndex(target, key = toPrimitive(key, true))
      && isObject(desc)
      && has(desc, 'value')
      && !has(desc, 'get')
      && !has(desc, 'set')
      // TODO: add validation descriptor w/o calling accessors
      && !desc.configurable
      && (!has(desc, 'writable') || desc.writable)
      && (!has(desc, 'enumerable') || desc.enumerable)
    ) {
      target[key] = desc.value;
      return target;
    } return dP(target, key, desc);
  };

  if (!ALL_CONSTRUCTORS) {
    $GOPD.f = $getDesc;
    $DP.f = $setDesc;
  }

  $export($export.S + $export.F * !ALL_CONSTRUCTORS, 'Object', {
    getOwnPropertyDescriptor: $getDesc,
    defineProperty: $setDesc
  });

  if (fails(function () { arrayToString.call({}); })) {
    arrayToString = arrayToLocaleString = function toString() {
      return arrayJoin.call(this);
    };
  }

  var $TypedArrayPrototype$ = redefineAll({}, proto);
  redefineAll($TypedArrayPrototype$, $iterators);
  hide($TypedArrayPrototype$, ITERATOR, $iterators.values);
  redefineAll($TypedArrayPrototype$, {
    slice: $slice,
    set: $set,
    constructor: function () { /* noop */ },
    toString: arrayToString,
    toLocaleString: $toLocaleString
  });
  addGetter($TypedArrayPrototype$, 'buffer', 'b');
  addGetter($TypedArrayPrototype$, 'byteOffset', 'o');
  addGetter($TypedArrayPrototype$, 'byteLength', 'l');
  addGetter($TypedArrayPrototype$, 'length', 'e');
  dP($TypedArrayPrototype$, TAG, {
    get: function () { return this[TYPED_ARRAY]; }
  });

  // eslint-disable-next-line max-statements
  module.exports = function (KEY, BYTES, wrapper, CLAMPED) {
    CLAMPED = !!CLAMPED;
    var NAME = KEY + (CLAMPED ? 'Clamped' : '') + 'Array';
    var GETTER = 'get' + KEY;
    var SETTER = 'set' + KEY;
    var TypedArray = global[NAME];
    var Base = TypedArray || {};
    var TAC = TypedArray && getPrototypeOf(TypedArray);
    var FORCED = !TypedArray || !$typed.ABV;
    var O = {};
    var TypedArrayPrototype = TypedArray && TypedArray[PROTOTYPE];
    var getter = function (that, index) {
      var data = that._d;
      return data.v[GETTER](index * BYTES + data.o, LITTLE_ENDIAN);
    };
    var setter = function (that, index, value) {
      var data = that._d;
      if (CLAMPED) value = (value = Math.round(value)) < 0 ? 0 : value > 0xff ? 0xff : value & 0xff;
      data.v[SETTER](index * BYTES + data.o, value, LITTLE_ENDIAN);
    };
    var addElement = function (that, index) {
      dP(that, index, {
        get: function () {
          return getter(this, index);
        },
        set: function (value) {
          return setter(this, index, value);
        },
        enumerable: true
      });
    };
    if (FORCED) {
      TypedArray = wrapper(function (that, data, $offset, $length) {
        anInstance(that, TypedArray, NAME, '_d');
        var index = 0;
        var offset = 0;
        var buffer, byteLength, length, klass;
        if (!isObject(data)) {
          length = toIndex(data);
          byteLength = length * BYTES;
          buffer = new $ArrayBuffer(byteLength);
        } else if (data instanceof $ArrayBuffer || (klass = classof(data)) == ARRAY_BUFFER || klass == SHARED_BUFFER) {
          buffer = data;
          offset = toOffset($offset, BYTES);
          var $len = data.byteLength;
          if ($length === undefined) {
            if ($len % BYTES) throw RangeError(WRONG_LENGTH);
            byteLength = $len - offset;
            if (byteLength < 0) throw RangeError(WRONG_LENGTH);
          } else {
            byteLength = toLength($length) * BYTES;
            if (byteLength + offset > $len) throw RangeError(WRONG_LENGTH);
          }
          length = byteLength / BYTES;
        } else if (TYPED_ARRAY in data) {
          return fromList(TypedArray, data);
        } else {
          return $from.call(TypedArray, data);
        }
        hide(that, '_d', {
          b: buffer,
          o: offset,
          l: byteLength,
          e: length,
          v: new $DataView(buffer)
        });
        while (index < length) addElement(that, index++);
      });
      TypedArrayPrototype = TypedArray[PROTOTYPE] = create($TypedArrayPrototype$);
      hide(TypedArrayPrototype, 'constructor', TypedArray);
    } else if (!fails(function () {
      TypedArray(1);
    }) || !fails(function () {
      new TypedArray(-1); // eslint-disable-line no-new
    }) || !$iterDetect(function (iter) {
      new TypedArray(); // eslint-disable-line no-new
      new TypedArray(null); // eslint-disable-line no-new
      new TypedArray(1.5); // eslint-disable-line no-new
      new TypedArray(iter); // eslint-disable-line no-new
    }, true)) {
      TypedArray = wrapper(function (that, data, $offset, $length) {
        anInstance(that, TypedArray, NAME);
        var klass;
        // `ws` module bug, temporarily remove validation length for Uint8Array
        // https://github.com/websockets/ws/pull/645
        if (!isObject(data)) return new Base(toIndex(data));
        if (data instanceof $ArrayBuffer || (klass = classof(data)) == ARRAY_BUFFER || klass == SHARED_BUFFER) {
          return $length !== undefined
            ? new Base(data, toOffset($offset, BYTES), $length)
            : $offset !== undefined
              ? new Base(data, toOffset($offset, BYTES))
              : new Base(data);
        }
        if (TYPED_ARRAY in data) return fromList(TypedArray, data);
        return $from.call(TypedArray, data);
      });
      arrayForEach(TAC !== Function.prototype ? gOPN(Base).concat(gOPN(TAC)) : gOPN(Base), function (key) {
        if (!(key in TypedArray)) hide(TypedArray, key, Base[key]);
      });
      TypedArray[PROTOTYPE] = TypedArrayPrototype;
      if (!LIBRARY) TypedArrayPrototype.constructor = TypedArray;
    }
    var $nativeIterator = TypedArrayPrototype[ITERATOR];
    var CORRECT_ITER_NAME = !!$nativeIterator
      && ($nativeIterator.name == 'values' || $nativeIterator.name == undefined);
    var $iterator = $iterators.values;
    hide(TypedArray, TYPED_CONSTRUCTOR, true);
    hide(TypedArrayPrototype, TYPED_ARRAY, NAME);
    hide(TypedArrayPrototype, VIEW, true);
    hide(TypedArrayPrototype, DEF_CONSTRUCTOR, TypedArray);

    if (CLAMPED ? new TypedArray(1)[TAG] != NAME : !(TAG in TypedArrayPrototype)) {
      dP(TypedArrayPrototype, TAG, {
        get: function () { return NAME; }
      });
    }

    O[NAME] = TypedArray;

    $export($export.G + $export.W + $export.F * (TypedArray != Base), O);

    $export($export.S, NAME, {
      BYTES_PER_ELEMENT: BYTES
    });

    $export($export.S + $export.F * fails(function () { Base.of.call(TypedArray, 1); }), NAME, {
      from: $from,
      of: $of
    });

    if (!(BYTES_PER_ELEMENT in TypedArrayPrototype)) hide(TypedArrayPrototype, BYTES_PER_ELEMENT, BYTES);

    $export($export.P, NAME, proto);

    setSpecies(NAME);

    $export($export.P + $export.F * FORCED_SET, NAME, { set: $set });

    $export($export.P + $export.F * !CORRECT_ITER_NAME, NAME, $iterators);

    if (!LIBRARY && TypedArrayPrototype.toString != arrayToString) TypedArrayPrototype.toString = arrayToString;

    $export($export.P + $export.F * fails(function () {
      new TypedArray(1).slice();
    }), NAME, { slice: $slice });

    $export($export.P + $export.F * (fails(function () {
      return [1, 2].toLocaleString() != new TypedArray([1, 2]).toLocaleString();
    }) || !fails(function () {
      TypedArrayPrototype.toLocaleString.call([1, 2]);
    })), NAME, { toLocaleString: $toLocaleString });

    Iterators[NAME] = CORRECT_ITER_NAME ? $nativeIterator : $iterator;
    if (!LIBRARY && !CORRECT_ITER_NAME) hide(TypedArrayPrototype, ITERATOR, $iterator);
  };
} else module.exports = function () { /* empty */ };


/***/ }),

/***/ 7221:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// to indexed object, toObject with fallback for non-array-like ES3 strings
var IObject = __webpack_require__(1249);
var defined = __webpack_require__(3344);
module.exports = function (it) {
  return IObject(defined(it));
};


/***/ }),

/***/ 7224:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";
// 21.1.3.6 String.prototype.endsWith(searchString [, endPosition])

var $export = __webpack_require__(2127);
var toLength = __webpack_require__(1485);
var context = __webpack_require__(8942);
var ENDS_WITH = 'endsWith';
var $endsWith = ''[ENDS_WITH];

$export($export.P + $export.F * __webpack_require__(5203)(ENDS_WITH), 'String', {
  endsWith: function endsWith(searchString /* , endPosition = @length */) {
    var that = context(this, searchString, ENDS_WITH);
    var endPosition = arguments.length > 1 ? arguments[1] : undefined;
    var len = toLength(that.length);
    var end = endPosition === undefined ? len : Math.min(toLength(endPosition), len);
    var search = String(searchString);
    return $endsWith
      ? $endsWith.call(that, search, end)
      : that.slice(end - search.length, end) === search;
  }
});


/***/ }),

/***/ 7227:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var $defineProperty = __webpack_require__(7967);
var createDesc = __webpack_require__(1996);

module.exports = function (object, index, value) {
  if (index in object) $defineProperty.f(object, index, createDesc(0, value));
  else object[index] = value;
};


/***/ }),

/***/ 7334:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// B.2.3.5 String.prototype.bold()
__webpack_require__(2468)('bold', function (createHTML) {
  return function bold() {
    return createHTML(this, 'b', '', '');
  };
});


/***/ }),

/***/ 7359:
/***/ (function(module) {

// 7.2.9 SameValue(x, y)
module.exports = Object.is || function is(x, y) {
  // eslint-disable-next-line no-self-compare
  return x === y ? x !== 0 || 1 / x === 1 / y : x != x && y != y;
};


/***/ }),

/***/ 7360:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// B.2.3.2 String.prototype.anchor(name)
__webpack_require__(2468)('anchor', function (createHTML) {
  return function anchor(name) {
    return createHTML(this, 'a', 'name', name);
  };
});


/***/ }),

/***/ 7368:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// call something on iterator step with safe closing on error
var anObject = __webpack_require__(4228);
module.exports = function (iterator, fn, value, entries) {
  try {
    return entries ? fn(anObject(value)[0], value[1]) : fn(value);
  // 7.4.6 IteratorClose(iterator, completion)
  } catch (e) {
    var ret = iterator['return'];
    if (ret !== undefined) anObject(ret.call(iterator));
    throw e;
  }
};


/***/ }),

/***/ 7452:
/***/ (function(module) {

/**
 * Copyright (c) 2014-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

var runtime = (function (exports) {
  "use strict";

  var Op = Object.prototype;
  var hasOwn = Op.hasOwnProperty;
  var defineProperty = Object.defineProperty || function (obj, key, desc) { obj[key] = desc.value; };
  var undefined; // More compressible than void 0.
  var $Symbol = typeof Symbol === "function" ? Symbol : {};
  var iteratorSymbol = $Symbol.iterator || "@@iterator";
  var asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator";
  var toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag";

  function define(obj, key, value) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
    return obj[key];
  }
  try {
    // IE 8 has a broken Object.defineProperty that only works on DOM objects.
    define({}, "");
  } catch (err) {
    define = function(obj, key, value) {
      return obj[key] = value;
    };
  }

  function wrap(innerFn, outerFn, self, tryLocsList) {
    // If outerFn provided and outerFn.prototype is a Generator, then outerFn.prototype instanceof Generator.
    var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator;
    var generator = Object.create(protoGenerator.prototype);
    var context = new Context(tryLocsList || []);

    // The ._invoke method unifies the implementations of the .next,
    // .throw, and .return methods.
    defineProperty(generator, "_invoke", { value: makeInvokeMethod(innerFn, self, context) });

    return generator;
  }
  exports.wrap = wrap;

  // Try/catch helper to minimize deoptimizations. Returns a completion
  // record like context.tryEntries[i].completion. This interface could
  // have been (and was previously) designed to take a closure to be
  // invoked without arguments, but in all the cases we care about we
  // already have an existing method we want to call, so there's no need
  // to create a new function object. We can even get away with assuming
  // the method takes exactly one argument, since that happens to be true
  // in every case, so we don't have to touch the arguments object. The
  // only additional allocation required is the completion record, which
  // has a stable shape and so hopefully should be cheap to allocate.
  function tryCatch(fn, obj, arg) {
    try {
      return { type: "normal", arg: fn.call(obj, arg) };
    } catch (err) {
      return { type: "throw", arg: err };
    }
  }

  var GenStateSuspendedStart = "suspendedStart";
  var GenStateSuspendedYield = "suspendedYield";
  var GenStateExecuting = "executing";
  var GenStateCompleted = "completed";

  // Returning this object from the innerFn has the same effect as
  // breaking out of the dispatch switch statement.
  var ContinueSentinel = {};

  // Dummy constructor functions that we use as the .constructor and
  // .constructor.prototype properties for functions that return Generator
  // objects. For full spec compliance, you may wish to configure your
  // minifier not to mangle the names of these two functions.
  function Generator() {}
  function GeneratorFunction() {}
  function GeneratorFunctionPrototype() {}

  // This is a polyfill for %IteratorPrototype% for environments that
  // don't natively support it.
  var IteratorPrototype = {};
  define(IteratorPrototype, iteratorSymbol, function () {
    return this;
  });

  var getProto = Object.getPrototypeOf;
  var NativeIteratorPrototype = getProto && getProto(getProto(values([])));
  if (NativeIteratorPrototype &&
      NativeIteratorPrototype !== Op &&
      hasOwn.call(NativeIteratorPrototype, iteratorSymbol)) {
    // This environment has a native %IteratorPrototype%; use it instead
    // of the polyfill.
    IteratorPrototype = NativeIteratorPrototype;
  }

  var Gp = GeneratorFunctionPrototype.prototype =
    Generator.prototype = Object.create(IteratorPrototype);
  GeneratorFunction.prototype = GeneratorFunctionPrototype;
  defineProperty(Gp, "constructor", { value: GeneratorFunctionPrototype, configurable: true });
  defineProperty(
    GeneratorFunctionPrototype,
    "constructor",
    { value: GeneratorFunction, configurable: true }
  );
  GeneratorFunction.displayName = define(
    GeneratorFunctionPrototype,
    toStringTagSymbol,
    "GeneratorFunction"
  );

  // Helper for defining the .next, .throw, and .return methods of the
  // Iterator interface in terms of a single ._invoke method.
  function defineIteratorMethods(prototype) {
    ["next", "throw", "return"].forEach(function(method) {
      define(prototype, method, function(arg) {
        return this._invoke(method, arg);
      });
    });
  }

  exports.isGeneratorFunction = function(genFun) {
    var ctor = typeof genFun === "function" && genFun.constructor;
    return ctor
      ? ctor === GeneratorFunction ||
        // For the native GeneratorFunction constructor, the best we can
        // do is to check its .name property.
        (ctor.displayName || ctor.name) === "GeneratorFunction"
      : false;
  };

  exports.mark = function(genFun) {
    if (Object.setPrototypeOf) {
      Object.setPrototypeOf(genFun, GeneratorFunctionPrototype);
    } else {
      genFun.__proto__ = GeneratorFunctionPrototype;
      define(genFun, toStringTagSymbol, "GeneratorFunction");
    }
    genFun.prototype = Object.create(Gp);
    return genFun;
  };

  // Within the body of any async function, `await x` is transformed to
  // `yield regeneratorRuntime.awrap(x)`, so that the runtime can test
  // `hasOwn.call(value, "__await")` to determine if the yielded value is
  // meant to be awaited.
  exports.awrap = function(arg) {
    return { __await: arg };
  };

  function AsyncIterator(generator, PromiseImpl) {
    function invoke(method, arg, resolve, reject) {
      var record = tryCatch(generator[method], generator, arg);
      if (record.type === "throw") {
        reject(record.arg);
      } else {
        var result = record.arg;
        var value = result.value;
        if (value &&
            typeof value === "object" &&
            hasOwn.call(value, "__await")) {
          return PromiseImpl.resolve(value.__await).then(function(value) {
            invoke("next", value, resolve, reject);
          }, function(err) {
            invoke("throw", err, resolve, reject);
          });
        }

        return PromiseImpl.resolve(value).then(function(unwrapped) {
          // When a yielded Promise is resolved, its final value becomes
          // the .value of the Promise<{value,done}> result for the
          // current iteration.
          result.value = unwrapped;
          resolve(result);
        }, function(error) {
          // If a rejected Promise was yielded, throw the rejection back
          // into the async generator function so it can be handled there.
          return invoke("throw", error, resolve, reject);
        });
      }
    }

    var previousPromise;

    function enqueue(method, arg) {
      function callInvokeWithMethodAndArg() {
        return new PromiseImpl(function(resolve, reject) {
          invoke(method, arg, resolve, reject);
        });
      }

      return previousPromise =
        // If enqueue has been called before, then we want to wait until
        // all previous Promises have been resolved before calling invoke,
        // so that results are always delivered in the correct order. If
        // enqueue has not been called before, then it is important to
        // call invoke immediately, without waiting on a callback to fire,
        // so that the async generator function has the opportunity to do
        // any necessary setup in a predictable way. This predictability
        // is why the Promise constructor synchronously invokes its
        // executor callback, and why async functions synchronously
        // execute code before the first await. Since we implement simple
        // async functions in terms of async generators, it is especially
        // important to get this right, even though it requires care.
        previousPromise ? previousPromise.then(
          callInvokeWithMethodAndArg,
          // Avoid propagating failures to Promises returned by later
          // invocations of the iterator.
          callInvokeWithMethodAndArg
        ) : callInvokeWithMethodAndArg();
    }

    // Define the unified helper method that is used to implement .next,
    // .throw, and .return (see defineIteratorMethods).
    defineProperty(this, "_invoke", { value: enqueue });
  }

  defineIteratorMethods(AsyncIterator.prototype);
  define(AsyncIterator.prototype, asyncIteratorSymbol, function () {
    return this;
  });
  exports.AsyncIterator = AsyncIterator;

  // Note that simple async functions are implemented on top of
  // AsyncIterator objects; they just return a Promise for the value of
  // the final result produced by the iterator.
  exports.async = function(innerFn, outerFn, self, tryLocsList, PromiseImpl) {
    if (PromiseImpl === void 0) PromiseImpl = Promise;

    var iter = new AsyncIterator(
      wrap(innerFn, outerFn, self, tryLocsList),
      PromiseImpl
    );

    return exports.isGeneratorFunction(outerFn)
      ? iter // If outerFn is a generator, return the full iterator.
      : iter.next().then(function(result) {
          return result.done ? result.value : iter.next();
        });
  };

  function makeInvokeMethod(innerFn, self, context) {
    var state = GenStateSuspendedStart;

    return function invoke(method, arg) {
      if (state === GenStateExecuting) {
        throw new Error("Generator is already running");
      }

      if (state === GenStateCompleted) {
        if (method === "throw") {
          throw arg;
        }

        // Be forgiving, per 25.3.3.3.3 of the spec:
        // https://people.mozilla.org/~jorendorff/es6-draft.html#sec-generatorresume
        return doneResult();
      }

      context.method = method;
      context.arg = arg;

      while (true) {
        var delegate = context.delegate;
        if (delegate) {
          var delegateResult = maybeInvokeDelegate(delegate, context);
          if (delegateResult) {
            if (delegateResult === ContinueSentinel) continue;
            return delegateResult;
          }
        }

        if (context.method === "next") {
          // Setting context._sent for legacy support of Babel's
          // function.sent implementation.
          context.sent = context._sent = context.arg;

        } else if (context.method === "throw") {
          if (state === GenStateSuspendedStart) {
            state = GenStateCompleted;
            throw context.arg;
          }

          context.dispatchException(context.arg);

        } else if (context.method === "return") {
          context.abrupt("return", context.arg);
        }

        state = GenStateExecuting;

        var record = tryCatch(innerFn, self, context);
        if (record.type === "normal") {
          // If an exception is thrown from innerFn, we leave state ===
          // GenStateExecuting and loop back for another invocation.
          state = context.done
            ? GenStateCompleted
            : GenStateSuspendedYield;

          if (record.arg === ContinueSentinel) {
            continue;
          }

          return {
            value: record.arg,
            done: context.done
          };

        } else if (record.type === "throw") {
          state = GenStateCompleted;
          // Dispatch the exception by looping back around to the
          // context.dispatchException(context.arg) call above.
          context.method = "throw";
          context.arg = record.arg;
        }
      }
    };
  }

  // Call delegate.iterator[context.method](context.arg) and handle the
  // result, either by returning a { value, done } result from the
  // delegate iterator, or by modifying context.method and context.arg,
  // setting context.delegate to null, and returning the ContinueSentinel.
  function maybeInvokeDelegate(delegate, context) {
    var methodName = context.method;
    var method = delegate.iterator[methodName];
    if (method === undefined) {
      // A .throw or .return when the delegate iterator has no .throw
      // method, or a missing .next mehtod, always terminate the
      // yield* loop.
      context.delegate = null;

      // Note: ["return"] must be used for ES3 parsing compatibility.
      if (methodName === "throw" && delegate.iterator["return"]) {
        // If the delegate iterator has a return method, give it a
        // chance to clean up.
        context.method = "return";
        context.arg = undefined;
        maybeInvokeDelegate(delegate, context);

        if (context.method === "throw") {
          // If maybeInvokeDelegate(context) changed context.method from
          // "return" to "throw", let that override the TypeError below.
          return ContinueSentinel;
        }
      }
      if (methodName !== "return") {
        context.method = "throw";
        context.arg = new TypeError(
          "The iterator does not provide a '" + methodName + "' method");
      }

      return ContinueSentinel;
    }

    var record = tryCatch(method, delegate.iterator, context.arg);

    if (record.type === "throw") {
      context.method = "throw";
      context.arg = record.arg;
      context.delegate = null;
      return ContinueSentinel;
    }

    var info = record.arg;

    if (! info) {
      context.method = "throw";
      context.arg = new TypeError("iterator result is not an object");
      context.delegate = null;
      return ContinueSentinel;
    }

    if (info.done) {
      // Assign the result of the finished delegate to the temporary
      // variable specified by delegate.resultName (see delegateYield).
      context[delegate.resultName] = info.value;

      // Resume execution at the desired location (see delegateYield).
      context.next = delegate.nextLoc;

      // If context.method was "throw" but the delegate handled the
      // exception, let the outer generator proceed normally. If
      // context.method was "next", forget context.arg since it has been
      // "consumed" by the delegate iterator. If context.method was
      // "return", allow the original .return call to continue in the
      // outer generator.
      if (context.method !== "return") {
        context.method = "next";
        context.arg = undefined;
      }

    } else {
      // Re-yield the result returned by the delegate method.
      return info;
    }

    // The delegate iterator is finished, so forget it and continue with
    // the outer generator.
    context.delegate = null;
    return ContinueSentinel;
  }

  // Define Generator.prototype.{next,throw,return} in terms of the
  // unified ._invoke helper method.
  defineIteratorMethods(Gp);

  define(Gp, toStringTagSymbol, "Generator");

  // A Generator should always return itself as the iterator object when the
  // @@iterator function is called on it. Some browsers' implementations of the
  // iterator prototype chain incorrectly implement this, causing the Generator
  // object to not be returned from this call. This ensures that doesn't happen.
  // See https://github.com/facebook/regenerator/issues/274 for more details.
  define(Gp, iteratorSymbol, function() {
    return this;
  });

  define(Gp, "toString", function() {
    return "[object Generator]";
  });

  function pushTryEntry(locs) {
    var entry = { tryLoc: locs[0] };

    if (1 in locs) {
      entry.catchLoc = locs[1];
    }

    if (2 in locs) {
      entry.finallyLoc = locs[2];
      entry.afterLoc = locs[3];
    }

    this.tryEntries.push(entry);
  }

  function resetTryEntry(entry) {
    var record = entry.completion || {};
    record.type = "normal";
    delete record.arg;
    entry.completion = record;
  }

  function Context(tryLocsList) {
    // The root entry object (effectively a try statement without a catch
    // or a finally block) gives us a place to store values thrown from
    // locations where there is no enclosing try statement.
    this.tryEntries = [{ tryLoc: "root" }];
    tryLocsList.forEach(pushTryEntry, this);
    this.reset(true);
  }

  exports.keys = function(val) {
    var object = Object(val);
    var keys = [];
    for (var key in object) {
      keys.push(key);
    }
    keys.reverse();

    // Rather than returning an object with a next method, we keep
    // things simple and return the next function itself.
    return function next() {
      while (keys.length) {
        var key = keys.pop();
        if (key in object) {
          next.value = key;
          next.done = false;
          return next;
        }
      }

      // To avoid creating an additional object, we just hang the .value
      // and .done properties off the next function object itself. This
      // also ensures that the minifier will not anonymize the function.
      next.done = true;
      return next;
    };
  };

  function values(iterable) {
    if (iterable) {
      var iteratorMethod = iterable[iteratorSymbol];
      if (iteratorMethod) {
        return iteratorMethod.call(iterable);
      }

      if (typeof iterable.next === "function") {
        return iterable;
      }

      if (!isNaN(iterable.length)) {
        var i = -1, next = function next() {
          while (++i < iterable.length) {
            if (hasOwn.call(iterable, i)) {
              next.value = iterable[i];
              next.done = false;
              return next;
            }
          }

          next.value = undefined;
          next.done = true;

          return next;
        };

        return next.next = next;
      }
    }

    // Return an iterator with no values.
    return { next: doneResult };
  }
  exports.values = values;

  function doneResult() {
    return { value: undefined, done: true };
  }

  Context.prototype = {
    constructor: Context,

    reset: function(skipTempReset) {
      this.prev = 0;
      this.next = 0;
      // Resetting context._sent for legacy support of Babel's
      // function.sent implementation.
      this.sent = this._sent = undefined;
      this.done = false;
      this.delegate = null;

      this.method = "next";
      this.arg = undefined;

      this.tryEntries.forEach(resetTryEntry);

      if (!skipTempReset) {
        for (var name in this) {
          // Not sure about the optimal order of these conditions:
          if (name.charAt(0) === "t" &&
              hasOwn.call(this, name) &&
              !isNaN(+name.slice(1))) {
            this[name] = undefined;
          }
        }
      }
    },

    stop: function() {
      this.done = true;

      var rootEntry = this.tryEntries[0];
      var rootRecord = rootEntry.completion;
      if (rootRecord.type === "throw") {
        throw rootRecord.arg;
      }

      return this.rval;
    },

    dispatchException: function(exception) {
      if (this.done) {
        throw exception;
      }

      var context = this;
      function handle(loc, caught) {
        record.type = "throw";
        record.arg = exception;
        context.next = loc;

        if (caught) {
          // If the dispatched exception was caught by a catch block,
          // then let that catch block handle the exception normally.
          context.method = "next";
          context.arg = undefined;
        }

        return !! caught;
      }

      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        var record = entry.completion;

        if (entry.tryLoc === "root") {
          // Exception thrown outside of any try block that could handle
          // it, so set the completion value of the entire function to
          // throw the exception.
          return handle("end");
        }

        if (entry.tryLoc <= this.prev) {
          var hasCatch = hasOwn.call(entry, "catchLoc");
          var hasFinally = hasOwn.call(entry, "finallyLoc");

          if (hasCatch && hasFinally) {
            if (this.prev < entry.catchLoc) {
              return handle(entry.catchLoc, true);
            } else if (this.prev < entry.finallyLoc) {
              return handle(entry.finallyLoc);
            }

          } else if (hasCatch) {
            if (this.prev < entry.catchLoc) {
              return handle(entry.catchLoc, true);
            }

          } else if (hasFinally) {
            if (this.prev < entry.finallyLoc) {
              return handle(entry.finallyLoc);
            }

          } else {
            throw new Error("try statement without catch or finally");
          }
        }
      }
    },

    abrupt: function(type, arg) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.tryLoc <= this.prev &&
            hasOwn.call(entry, "finallyLoc") &&
            this.prev < entry.finallyLoc) {
          var finallyEntry = entry;
          break;
        }
      }

      if (finallyEntry &&
          (type === "break" ||
           type === "continue") &&
          finallyEntry.tryLoc <= arg &&
          arg <= finallyEntry.finallyLoc) {
        // Ignore the finally entry if control is not jumping to a
        // location outside the try/catch block.
        finallyEntry = null;
      }

      var record = finallyEntry ? finallyEntry.completion : {};
      record.type = type;
      record.arg = arg;

      if (finallyEntry) {
        this.method = "next";
        this.next = finallyEntry.finallyLoc;
        return ContinueSentinel;
      }

      return this.complete(record);
    },

    complete: function(record, afterLoc) {
      if (record.type === "throw") {
        throw record.arg;
      }

      if (record.type === "break" ||
          record.type === "continue") {
        this.next = record.arg;
      } else if (record.type === "return") {
        this.rval = this.arg = record.arg;
        this.method = "return";
        this.next = "end";
      } else if (record.type === "normal" && afterLoc) {
        this.next = afterLoc;
      }

      return ContinueSentinel;
    },

    finish: function(finallyLoc) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.finallyLoc === finallyLoc) {
          this.complete(entry.completion, entry.afterLoc);
          resetTryEntry(entry);
          return ContinueSentinel;
        }
      }
    },

    "catch": function(tryLoc) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.tryLoc === tryLoc) {
          var record = entry.completion;
          if (record.type === "throw") {
            var thrown = record.arg;
            resetTryEntry(entry);
          }
          return thrown;
        }
      }

      // The context.catch method must only be called with a location
      // argument that corresponds to a known catch block.
      throw new Error("illegal catch attempt");
    },

    delegateYield: function(iterable, resultName, nextLoc) {
      this.delegate = {
        iterator: values(iterable),
        resultName: resultName,
        nextLoc: nextLoc
      };

      if (this.method === "next") {
        // Deliberately forget the last sent value so that we don't
        // accidentally pass it on to the delegate.
        this.arg = undefined;
      }

      return ContinueSentinel;
    }
  };

  // Regardless of whether this script is executing as a CommonJS module
  // or not, return the runtime object so that we can declare the variable
  // regeneratorRuntime in the outer scope, which allows this module to be
  // injected easily by `bin/regenerator --include-runtime script.js`.
  return exports;

}(
  // If this script is executing as a CommonJS module, use module.exports
  // as the regeneratorRuntime namespace. Otherwise create a new empty
  // object. Either way, the resulting object will be used to initialize
  // the regeneratorRuntime variable at the top of this file.
   true ? module.exports : 0
));

try {
  regeneratorRuntime = runtime;
} catch (accidentalStrictMode) {
  // This module should not be running in strict mode, so the above
  // assignment should always work unless something is misconfigured. Just
  // in case runtime.js accidentally runs in strict mode, in modern engines
  // we can explicitly access globalThis. In older engines we can escape
  // strict mode using a global Function call. This could conceivably fail
  // if a Content Security Policy forbids using Function, but in that case
  // the proper solution is to fix the accidental strict mode problem. If
  // you've misconfigured your bundler to force strict mode and applied a
  // CSP to forbid Function, and you're not willing to fix either of those
  // problems, please detail your unique predicament in a GitHub issue.
  if (typeof globalThis === "object") {
    globalThis.regeneratorRuntime = runtime;
  } else {
    Function("r", "regeneratorRuntime = r")(runtime);
  }
}


/***/ }),

/***/ 7461:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";


__webpack_require__(4572);

var _global = _interopRequireDefault(__webpack_require__(5104));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

if (_global["default"]._babelPolyfill && typeof console !== "undefined" && console.warn) {
  console.warn("@babel/polyfill is loaded more than once on this page. This is probably not desirable/intended " + "and may have consequences if different versions of the polyfills are applied sequentially. " + "If you do need to load the polyfill more than once, use @babel/polyfill/noConflict " + "instead to bypass the warning.");
}

_global["default"]._babelPolyfill = true;

/***/ }),

/***/ 7482:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// 19.1.3.6 Object.prototype.toString()
var classof = __webpack_require__(4848);
var test = {};
test[__webpack_require__(7574)('toStringTag')] = 'z';
if (test + '' != '[object z]') {
  __webpack_require__(8859)(Object.prototype, 'toString', function toString() {
    return '[object ' + classof(this) + ']';
  }, true);
}


/***/ }),

/***/ 7509:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.2.2.21 Math.log10(x)
var $export = __webpack_require__(2127);

$export($export.S, 'Math', {
  log10: function log10(x) {
    return Math.log(x) * Math.LOG10E;
  }
});


/***/ }),

/***/ 7526:
/***/ (function(module) {

// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
var global = module.exports = typeof window != 'undefined' && window.Math == Math
  ? window : typeof self != 'undefined' && self.Math == Math ? self
  // eslint-disable-next-line no-new-func
  : Function('return this')();
if (typeof __g == 'number') __g = global; // eslint-disable-line no-undef


/***/ }),

/***/ 7574:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var store = __webpack_require__(4556)('wks');
var uid = __webpack_require__(4415);
var Symbol = (__webpack_require__(7526).Symbol);
var USE_SYMBOL = typeof Symbol == 'function';

var $exports = module.exports = function (name) {
  return store[name] || (store[name] =
    USE_SYMBOL && Symbol[name] || (USE_SYMBOL ? Symbol : uid)('Symbol.' + name));
};

$exports.store = store;


/***/ }),

/***/ 7594:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// https://github.com/tc39/proposal-object-values-entries
var $export = __webpack_require__(2127);
var $values = __webpack_require__(3854)(false);

$export($export.S, 'Object', {
  values: function values(it) {
    return $values(it);
  }
});


/***/ }),

/***/ 7727:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var $export = __webpack_require__(2127);
var toInteger = __webpack_require__(7087);
var aNumberValue = __webpack_require__(5122);
var repeat = __webpack_require__(7926);
var $toFixed = 1.0.toFixed;
var floor = Math.floor;
var data = [0, 0, 0, 0, 0, 0];
var ERROR = 'Number.toFixed: incorrect invocation!';
var ZERO = '0';

var multiply = function (n, c) {
  var i = -1;
  var c2 = c;
  while (++i < 6) {
    c2 += n * data[i];
    data[i] = c2 % 1e7;
    c2 = floor(c2 / 1e7);
  }
};
var divide = function (n) {
  var i = 6;
  var c = 0;
  while (--i >= 0) {
    c += data[i];
    data[i] = floor(c / n);
    c = (c % n) * 1e7;
  }
};
var numToString = function () {
  var i = 6;
  var s = '';
  while (--i >= 0) {
    if (s !== '' || i === 0 || data[i] !== 0) {
      var t = String(data[i]);
      s = s === '' ? t : s + repeat.call(ZERO, 7 - t.length) + t;
    }
  } return s;
};
var pow = function (x, n, acc) {
  return n === 0 ? acc : n % 2 === 1 ? pow(x, n - 1, acc * x) : pow(x * x, n / 2, acc);
};
var log = function (x) {
  var n = 0;
  var x2 = x;
  while (x2 >= 4096) {
    n += 12;
    x2 /= 4096;
  }
  while (x2 >= 2) {
    n += 1;
    x2 /= 2;
  } return n;
};

$export($export.P + $export.F * (!!$toFixed && (
  0.00008.toFixed(3) !== '0.000' ||
  0.9.toFixed(0) !== '1' ||
  1.255.toFixed(2) !== '1.25' ||
  1000000000000000128.0.toFixed(0) !== '1000000000000000128'
) || !__webpack_require__(9448)(function () {
  // V8 ~ Android 4.3-
  $toFixed.call({});
})), 'Number', {
  toFixed: function toFixed(fractionDigits) {
    var x = aNumberValue(this, ERROR);
    var f = toInteger(fractionDigits);
    var s = '';
    var m = ZERO;
    var e, z, j, k;
    if (f < 0 || f > 20) throw RangeError(ERROR);
    // eslint-disable-next-line no-self-compare
    if (x != x) return 'NaN';
    if (x <= -1e21 || x >= 1e21) return String(x);
    if (x < 0) {
      s = '-';
      x = -x;
    }
    if (x > 1e-21) {
      e = log(x * pow(2, 69, 1)) - 69;
      z = e < 0 ? x * pow(2, -e, 1) : x / pow(2, e, 1);
      z *= 0x10000000000000;
      e = 52 - e;
      if (e > 0) {
        multiply(0, z);
        j = f;
        while (j >= 7) {
          multiply(1e7, 0);
          j -= 7;
        }
        multiply(pow(10, j, 1), 0);
        j = e - 1;
        while (j >= 23) {
          divide(1 << 23);
          j -= 23;
        }
        divide(1 << j);
        multiply(1, 1);
        divide(2);
        m = numToString();
      } else {
        multiply(0, z);
        multiply(1 << -e, 0);
        m = numToString() + repeat.call(ZERO, f);
      }
    }
    if (f > 0) {
      k = m.length;
      m = s + (k <= f ? '0.' + repeat.call(ZERO, f - k) + m : m.slice(0, k - f) + '.' + m.slice(k - f));
    } else {
      m = s + m;
    } return m;
  }
});


/***/ }),

/***/ 7739:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(2820);
module.exports = (__webpack_require__(7960).f)('asyncIterator');


/***/ }),

/***/ 7762:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 22.1.3.6 Array.prototype.fill(value, start = 0, end = this.length)
var $export = __webpack_require__(2127);

$export($export.P, 'Array', { fill: __webpack_require__(5564) });

__webpack_require__(8184)('fill');


/***/ }),

/***/ 7849:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

var DateProto = Date.prototype;
var INVALID_DATE = 'Invalid Date';
var TO_STRING = 'toString';
var $toString = DateProto[TO_STRING];
var getTime = DateProto.getTime;
if (new Date(NaN) + '' != INVALID_DATE) {
  __webpack_require__(8859)(DateProto, TO_STRING, function toString() {
    var value = getTime.call(this);
    // eslint-disable-next-line no-self-compare
    return value === value ? $toString.call(this) : INVALID_DATE;
  });
}


/***/ }),

/***/ 7874:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var $export = __webpack_require__(2127);
var $reduce = __webpack_require__(6543);

$export($export.P + $export.F * !__webpack_require__(6884)([].reduceRight, true), 'Array', {
  // 22.1.3.19 / 15.4.4.22 Array.prototype.reduceRight(callbackfn [, initialValue])
  reduceRight: function reduceRight(callbackfn /* , initialValue */) {
    return $reduce(this, callbackfn, arguments.length, arguments[1], true);
  }
});


/***/ }),

/***/ 7899:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 22.1.2.2 / 15.4.3.2 Array.isArray(arg)
var $export = __webpack_require__(2127);

$export($export.S, 'Array', { isArray: __webpack_require__(7981) });


/***/ }),

/***/ 7901:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.2.2.33 Math.tanh(x)
var $export = __webpack_require__(2127);
var expm1 = __webpack_require__(5551);
var exp = Math.exp;

$export($export.S, 'Math', {
  tanh: function tanh(x) {
    var a = expm1(x = +x);
    var b = expm1(-x);
    return a == Infinity ? 1 : b == Infinity ? -1 : (a - b) / (exp(x) + exp(-x));
  }
});


/***/ }),

/***/ 7917:
/***/ (function(module) {

var hasOwnProperty = {}.hasOwnProperty;
module.exports = function (it, key) {
  return hasOwnProperty.call(it, key);
};


/***/ }),

/***/ 7925:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(7209)('Float32', 4, function (init) {
  return function Float32Array(data, byteOffset, length) {
    return init(this, data, byteOffset, length);
  };
});


/***/ }),

/***/ 7926:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var toInteger = __webpack_require__(7087);
var defined = __webpack_require__(3344);

module.exports = function repeat(count) {
  var str = String(defined(this));
  var res = '';
  var n = toInteger(count);
  if (n < 0 || n == Infinity) throw RangeError("Count can't be negative");
  for (;n > 0; (n >>>= 1) && (str += str)) if (n & 1) res += str;
  return res;
};


/***/ }),

/***/ 7960:
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

exports.f = __webpack_require__(7574);


/***/ }),

/***/ 7967:
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

var anObject = __webpack_require__(4228);
var IE8_DOM_DEFINE = __webpack_require__(2956);
var toPrimitive = __webpack_require__(3048);
var dP = Object.defineProperty;

exports.f = __webpack_require__(1763) ? Object.defineProperty : function defineProperty(O, P, Attributes) {
  anObject(O);
  P = toPrimitive(P, true);
  anObject(Attributes);
  if (IE8_DOM_DEFINE) try {
    return dP(O, P, Attributes);
  } catch (e) { /* empty */ }
  if ('get' in Attributes || 'set' in Attributes) throw TypeError('Accessors not supported!');
  if ('value' in Attributes) O[P] = Attributes.value;
  return O;
};


/***/ }),

/***/ 7981:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// 7.2.2 IsArray(argument)
var cof = __webpack_require__(5089);
module.exports = Array.isArray || function isArray(arg) {
  return cof(arg) == 'Array';
};


/***/ }),

/***/ 8032:
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";

var global = __webpack_require__(7526);
var DESCRIPTORS = __webpack_require__(1763);
var LIBRARY = __webpack_require__(2750);
var $typed = __webpack_require__(237);
var hide = __webpack_require__(3341);
var redefineAll = __webpack_require__(6065);
var fails = __webpack_require__(9448);
var anInstance = __webpack_require__(6440);
var toInteger = __webpack_require__(7087);
var toLength = __webpack_require__(1485);
var toIndex = __webpack_require__(3133);
var gOPN = (__webpack_require__(9415).f);
var dP = (__webpack_require__(7967).f);
var arrayFill = __webpack_require__(5564);
var setToStringTag = __webpack_require__(3844);
var ARRAY_BUFFER = 'ArrayBuffer';
var DATA_VIEW = 'DataView';
var PROTOTYPE = 'prototype';
var WRONG_LENGTH = 'Wrong length!';
var WRONG_INDEX = 'Wrong index!';
var $ArrayBuffer = global[ARRAY_BUFFER];
var $DataView = global[DATA_VIEW];
var Math = global.Math;
var RangeError = global.RangeError;
// eslint-disable-next-line no-shadow-restricted-names
var Infinity = global.Infinity;
var BaseBuffer = $ArrayBuffer;
var abs = Math.abs;
var pow = Math.pow;
var floor = Math.floor;
var log = Math.log;
var LN2 = Math.LN2;
var BUFFER = 'buffer';
var BYTE_LENGTH = 'byteLength';
var BYTE_OFFSET = 'byteOffset';
var $BUFFER = DESCRIPTORS ? '_b' : BUFFER;
var $LENGTH = DESCRIPTORS ? '_l' : BYTE_LENGTH;
var $OFFSET = DESCRIPTORS ? '_o' : BYTE_OFFSET;

// IEEE754 conversions based on https://github.com/feross/ieee754
function packIEEE754(value, mLen, nBytes) {
  var buffer = new Array(nBytes);
  var eLen = nBytes * 8 - mLen - 1;
  var eMax = (1 << eLen) - 1;
  var eBias = eMax >> 1;
  var rt = mLen === 23 ? pow(2, -24) - pow(2, -77) : 0;
  var i = 0;
  var s = value < 0 || value === 0 && 1 / value < 0 ? 1 : 0;
  var e, m, c;
  value = abs(value);
  // eslint-disable-next-line no-self-compare
  if (value != value || value === Infinity) {
    // eslint-disable-next-line no-self-compare
    m = value != value ? 1 : 0;
    e = eMax;
  } else {
    e = floor(log(value) / LN2);
    if (value * (c = pow(2, -e)) < 1) {
      e--;
      c *= 2;
    }
    if (e + eBias >= 1) {
      value += rt / c;
    } else {
      value += rt * pow(2, 1 - eBias);
    }
    if (value * c >= 2) {
      e++;
      c /= 2;
    }
    if (e + eBias >= eMax) {
      m = 0;
      e = eMax;
    } else if (e + eBias >= 1) {
      m = (value * c - 1) * pow(2, mLen);
      e = e + eBias;
    } else {
      m = value * pow(2, eBias - 1) * pow(2, mLen);
      e = 0;
    }
  }
  for (; mLen >= 8; buffer[i++] = m & 255, m /= 256, mLen -= 8);
  e = e << mLen | m;
  eLen += mLen;
  for (; eLen > 0; buffer[i++] = e & 255, e /= 256, eLen -= 8);
  buffer[--i] |= s * 128;
  return buffer;
}
function unpackIEEE754(buffer, mLen, nBytes) {
  var eLen = nBytes * 8 - mLen - 1;
  var eMax = (1 << eLen) - 1;
  var eBias = eMax >> 1;
  var nBits = eLen - 7;
  var i = nBytes - 1;
  var s = buffer[i--];
  var e = s & 127;
  var m;
  s >>= 7;
  for (; nBits > 0; e = e * 256 + buffer[i], i--, nBits -= 8);
  m = e & (1 << -nBits) - 1;
  e >>= -nBits;
  nBits += mLen;
  for (; nBits > 0; m = m * 256 + buffer[i], i--, nBits -= 8);
  if (e === 0) {
    e = 1 - eBias;
  } else if (e === eMax) {
    return m ? NaN : s ? -Infinity : Infinity;
  } else {
    m = m + pow(2, mLen);
    e = e - eBias;
  } return (s ? -1 : 1) * m * pow(2, e - mLen);
}

function unpackI32(bytes) {
  return bytes[3] << 24 | bytes[2] << 16 | bytes[1] << 8 | bytes[0];
}
function packI8(it) {
  return [it & 0xff];
}
function packI16(it) {
  return [it & 0xff, it >> 8 & 0xff];
}
function packI32(it) {
  return [it & 0xff, it >> 8 & 0xff, it >> 16 & 0xff, it >> 24 & 0xff];
}
function packF64(it) {
  return packIEEE754(it, 52, 8);
}
function packF32(it) {
  return packIEEE754(it, 23, 4);
}

function addGetter(C, key, internal) {
  dP(C[PROTOTYPE], key, { get: function () { return this[internal]; } });
}

function get(view, bytes, index, isLittleEndian) {
  var numIndex = +index;
  var intIndex = toIndex(numIndex);
  if (intIndex + bytes > view[$LENGTH]) throw RangeError(WRONG_INDEX);
  var store = view[$BUFFER]._b;
  var start = intIndex + view[$OFFSET];
  var pack = store.slice(start, start + bytes);
  return isLittleEndian ? pack : pack.reverse();
}
function set(view, bytes, index, conversion, value, isLittleEndian) {
  var numIndex = +index;
  var intIndex = toIndex(numIndex);
  if (intIndex + bytes > view[$LENGTH]) throw RangeError(WRONG_INDEX);
  var store = view[$BUFFER]._b;
  var start = intIndex + view[$OFFSET];
  var pack = conversion(+value);
  for (var i = 0; i < bytes; i++) store[start + i] = pack[isLittleEndian ? i : bytes - i - 1];
}

if (!$typed.ABV) {
  $ArrayBuffer = function ArrayBuffer(length) {
    anInstance(this, $ArrayBuffer, ARRAY_BUFFER);
    var byteLength = toIndex(length);
    this._b = arrayFill.call(new Array(byteLength), 0);
    this[$LENGTH] = byteLength;
  };

  $DataView = function DataView(buffer, byteOffset, byteLength) {
    anInstance(this, $DataView, DATA_VIEW);
    anInstance(buffer, $ArrayBuffer, DATA_VIEW);
    var bufferLength = buffer[$LENGTH];
    var offset = toInteger(byteOffset);
    if (offset < 0 || offset > bufferLength) throw RangeError('Wrong offset!');
    byteLength = byteLength === undefined ? bufferLength - offset : toLength(byteLength);
    if (offset + byteLength > bufferLength) throw RangeError(WRONG_LENGTH);
    this[$BUFFER] = buffer;
    this[$OFFSET] = offset;
    this[$LENGTH] = byteLength;
  };

  if (DESCRIPTORS) {
    addGetter($ArrayBuffer, BYTE_LENGTH, '_l');
    addGetter($DataView, BUFFER, '_b');
    addGetter($DataView, BYTE_LENGTH, '_l');
    addGetter($DataView, BYTE_OFFSET, '_o');
  }

  redefineAll($DataView[PROTOTYPE], {
    getInt8: function getInt8(byteOffset) {
      return get(this, 1, byteOffset)[0] << 24 >> 24;
    },
    getUint8: function getUint8(byteOffset) {
      return get(this, 1, byteOffset)[0];
    },
    getInt16: function getInt16(byteOffset /* , littleEndian */) {
      var bytes = get(this, 2, byteOffset, arguments[1]);
      return (bytes[1] << 8 | bytes[0]) << 16 >> 16;
    },
    getUint16: function getUint16(byteOffset /* , littleEndian */) {
      var bytes = get(this, 2, byteOffset, arguments[1]);
      return bytes[1] << 8 | bytes[0];
    },
    getInt32: function getInt32(byteOffset /* , littleEndian */) {
      return unpackI32(get(this, 4, byteOffset, arguments[1]));
    },
    getUint32: function getUint32(byteOffset /* , littleEndian */) {
      return unpackI32(get(this, 4, byteOffset, arguments[1])) >>> 0;
    },
    getFloat32: function getFloat32(byteOffset /* , littleEndian */) {
      return unpackIEEE754(get(this, 4, byteOffset, arguments[1]), 23, 4);
    },
    getFloat64: function getFloat64(byteOffset /* , littleEndian */) {
      return unpackIEEE754(get(this, 8, byteOffset, arguments[1]), 52, 8);
    },
    setInt8: function setInt8(byteOffset, value) {
      set(this, 1, byteOffset, packI8, value);
    },
    setUint8: function setUint8(byteOffset, value) {
      set(this, 1, byteOffset, packI8, value);
    },
    setInt16: function setInt16(byteOffset, value /* , littleEndian */) {
      set(this, 2, byteOffset, packI16, value, arguments[2]);
    },
    setUint16: function setUint16(byteOffset, value /* , littleEndian */) {
      set(this, 2, byteOffset, packI16, value, arguments[2]);
    },
    setInt32: function setInt32(byteOffset, value /* , littleEndian */) {
      set(this, 4, byteOffset, packI32, value, arguments[2]);
    },
    setUint32: function setUint32(byteOffset, value /* , littleEndian */) {
      set(this, 4, byteOffset, packI32, value, arguments[2]);
    },
    setFloat32: function setFloat32(byteOffset, value /* , littleEndian */) {
      set(this, 4, byteOffset, packF32, value, arguments[2]);
    },
    setFloat64: function setFloat64(byteOffset, value /* , littleEndian */) {
      set(this, 8, byteOffset, packF64, value, arguments[2]);
    }
  });
} else {
  if (!fails(function () {
    $ArrayBuffer(1);
  }) || !fails(function () {
    new $ArrayBuffer(-1); // eslint-disable-line no-new
  }) || fails(function () {
    new $ArrayBuffer(); // eslint-disable-line no-new
    new $ArrayBuffer(1.5); // eslint-disable-line no-new
    new $ArrayBuffer(NaN); // eslint-disable-line no-new
    return $ArrayBuffer.name != ARRAY_BUFFER;
  })) {
    $ArrayBuffer = function ArrayBuffer(length) {
      anInstance(this, $ArrayBuffer);
      return new BaseBuffer(toIndex(length));
    };
    var ArrayBufferProto = $ArrayBuffer[PROTOTYPE] = BaseBuffer[PROTOTYPE];
    for (var keys = gOPN(BaseBuffer), j = 0, key; keys.length > j;) {
      if (!((key = keys[j++]) in $ArrayBuffer)) hide($ArrayBuffer, key, BaseBuffer[key]);
    }
    if (!LIBRARY) ArrayBufferProto.constructor = $ArrayBuffer;
  }
  // iOS Safari 7.x bug
  var view = new $DataView(new $ArrayBuffer(2));
  var $setInt8 = $DataView[PROTOTYPE].setInt8;
  view.setInt8(0, 2147483648);
  view.setInt8(1, 2147483649);
  if (view.getInt8(0) || !view.getInt8(1)) redefineAll($DataView[PROTOTYPE], {
    setInt8: function setInt8(byteOffset, value) {
      $setInt8.call(this, byteOffset, value << 24 >> 24);
    },
    setUint8: function setUint8(byteOffset, value) {
      $setInt8.call(this, byteOffset, value << 24 >> 24);
    }
  }, true);
}
setToStringTag($ArrayBuffer, ARRAY_BUFFER);
setToStringTag($DataView, DATA_VIEW);
hide($DataView[PROTOTYPE], $typed.VIEW, true);
exports[ARRAY_BUFFER] = $ArrayBuffer;
exports[DATA_VIEW] = $DataView;


/***/ }),

/***/ 8050:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

var $export = __webpack_require__(2127);
var $parseInt = __webpack_require__(2738);
// 20.1.2.13 Number.parseInt(string, radix)
$export($export.S + $export.F * (Number.parseInt != $parseInt), 'Number', { parseInt: $parseInt });


/***/ }),

/***/ 8066:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(7209)('Int32', 4, function (init) {
  return function Int32Array(data, byteOffset, length) {
    return init(this, data, byteOffset, length);
  };
});


/***/ }),

/***/ 8128:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(9087);
module.exports = __webpack_require__(6094).Array.includes;


/***/ }),

/***/ 8132:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 19.1.3.19 Object.setPrototypeOf(O, proto)
var $export = __webpack_require__(2127);
$export($export.S, 'Object', { setPrototypeOf: (__webpack_require__(5170).set) });


/***/ }),

/***/ 8163:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var weak = __webpack_require__(9882);
var validate = __webpack_require__(2888);
var WEAK_SET = 'WeakSet';

// 23.4 WeakSet Objects
__webpack_require__(8933)(WEAK_SET, function (get) {
  return function WeakSet() { return get(this, arguments.length > 0 ? arguments[0] : undefined); };
}, {
  // 23.4.3.1 WeakSet.prototype.add(value)
  add: function add(value) {
    return weak.def(validate(this, WEAK_SET), value, true);
  }
}, weak, false, true);


/***/ }),

/***/ 8175:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var LIBRARY = __webpack_require__(2750);
var $export = __webpack_require__(2127);
var redefine = __webpack_require__(8859);
var hide = __webpack_require__(3341);
var Iterators = __webpack_require__(906);
var $iterCreate = __webpack_require__(6032);
var setToStringTag = __webpack_require__(3844);
var getPrototypeOf = __webpack_require__(627);
var ITERATOR = __webpack_require__(7574)('iterator');
var BUGGY = !([].keys && 'next' in [].keys()); // Safari has buggy iterators w/o `next`
var FF_ITERATOR = '@@iterator';
var KEYS = 'keys';
var VALUES = 'values';

var returnThis = function () { return this; };

module.exports = function (Base, NAME, Constructor, next, DEFAULT, IS_SET, FORCED) {
  $iterCreate(Constructor, NAME, next);
  var getMethod = function (kind) {
    if (!BUGGY && kind in proto) return proto[kind];
    switch (kind) {
      case KEYS: return function keys() { return new Constructor(this, kind); };
      case VALUES: return function values() { return new Constructor(this, kind); };
    } return function entries() { return new Constructor(this, kind); };
  };
  var TAG = NAME + ' Iterator';
  var DEF_VALUES = DEFAULT == VALUES;
  var VALUES_BUG = false;
  var proto = Base.prototype;
  var $native = proto[ITERATOR] || proto[FF_ITERATOR] || DEFAULT && proto[DEFAULT];
  var $default = $native || getMethod(DEFAULT);
  var $entries = DEFAULT ? !DEF_VALUES ? $default : getMethod('entries') : undefined;
  var $anyNative = NAME == 'Array' ? proto.entries || $native : $native;
  var methods, key, IteratorPrototype;
  // Fix native
  if ($anyNative) {
    IteratorPrototype = getPrototypeOf($anyNative.call(new Base()));
    if (IteratorPrototype !== Object.prototype && IteratorPrototype.next) {
      // Set @@toStringTag to native iterators
      setToStringTag(IteratorPrototype, TAG, true);
      // fix for some old engines
      if (!LIBRARY && typeof IteratorPrototype[ITERATOR] != 'function') hide(IteratorPrototype, ITERATOR, returnThis);
    }
  }
  // fix Array#{values, @@iterator}.name in V8 / FF
  if (DEF_VALUES && $native && $native.name !== VALUES) {
    VALUES_BUG = true;
    $default = function values() { return $native.call(this); };
  }
  // Define iterator
  if ((!LIBRARY || FORCED) && (BUGGY || VALUES_BUG || !proto[ITERATOR])) {
    hide(proto, ITERATOR, $default);
  }
  // Plug for library
  Iterators[NAME] = $default;
  Iterators[TAG] = returnThis;
  if (DEFAULT) {
    methods = {
      values: DEF_VALUES ? $default : getMethod(VALUES),
      keys: IS_SET ? $default : getMethod(KEYS),
      entries: $entries
    };
    if (FORCED) for (key in methods) {
      if (!(key in proto)) redefine(proto, key, methods[key]);
    } else $export($export.P + $export.F * (BUGGY || VALUES_BUG), NAME, methods);
  }
  return methods;
};


/***/ }),

/***/ 8184:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// 22.1.3.31 Array.prototype[@@unscopables]
var UNSCOPABLES = __webpack_require__(7574)('unscopables');
var ArrayProto = Array.prototype;
if (ArrayProto[UNSCOPABLES] == undefined) __webpack_require__(3341)(ArrayProto, UNSCOPABLES, {});
module.exports = function (key) {
  ArrayProto[UNSCOPABLES][key] = true;
};


/***/ }),

/***/ 8206:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// 19.1.2.1 Object.assign(target, source, ...)
var DESCRIPTORS = __webpack_require__(1763);
var getKeys = __webpack_require__(1311);
var gOPS = __webpack_require__(1060);
var pIE = __webpack_require__(8449);
var toObject = __webpack_require__(8270);
var IObject = __webpack_require__(1249);
var $assign = Object.assign;

// should work with symbols and should have deterministic property order (V8 bug)
module.exports = !$assign || __webpack_require__(9448)(function () {
  var A = {};
  var B = {};
  // eslint-disable-next-line no-undef
  var S = Symbol();
  var K = 'abcdefghijklmnopqrst';
  A[S] = 7;
  K.split('').forEach(function (k) { B[k] = k; });
  return $assign({}, A)[S] != 7 || Object.keys($assign({}, B)).join('') != K;
}) ? function assign(target, source) { // eslint-disable-line no-unused-vars
  var T = toObject(target);
  var aLen = arguments.length;
  var index = 1;
  var getSymbols = gOPS.f;
  var isEnum = pIE.f;
  while (aLen > index) {
    var S = IObject(arguments[index++]);
    var keys = getSymbols ? getKeys(S).concat(getSymbols(S)) : getKeys(S);
    var length = keys.length;
    var j = 0;
    var key;
    while (length > j) {
      key = keys[j++];
      if (!DESCRIPTORS || isEnum.call(S, key)) T[key] = S[key];
    }
  } return T;
} : $assign;


/***/ }),

/***/ 8219:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// Thank's IE8 for his funny defineProperty
module.exports = !__webpack_require__(1984)(function () {
  return Object.defineProperty({}, 'a', { get: function () { return 7; } }).a != 7;
});


/***/ }),

/***/ 8236:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 19.1.2.5 Object.freeze(O)
var isObject = __webpack_require__(3305);
var meta = (__webpack_require__(2988).onFreeze);

__webpack_require__(923)('freeze', function ($freeze) {
  return function freeze(it) {
    return $freeze && isObject(it) ? $freeze(meta(it)) : it;
  };
});


/***/ }),

/***/ 8270:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// 7.1.13 ToObject(argument)
var defined = __webpack_require__(3344);
module.exports = function (it) {
  return Object(defined(it));
};


/***/ }),

/***/ 8301:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

var global = __webpack_require__(7526);
var inheritIfRequired = __webpack_require__(8880);
var dP = (__webpack_require__(7967).f);
var gOPN = (__webpack_require__(9415).f);
var isRegExp = __webpack_require__(5411);
var $flags = __webpack_require__(1158);
var $RegExp = global.RegExp;
var Base = $RegExp;
var proto = $RegExp.prototype;
var re1 = /a/g;
var re2 = /a/g;
// "new" creates a new object, old webkit buggy here
var CORRECT_NEW = new $RegExp(re1) !== re1;

if (__webpack_require__(1763) && (!CORRECT_NEW || __webpack_require__(9448)(function () {
  re2[__webpack_require__(7574)('match')] = false;
  // RegExp constructor can alter flags and IsRegExp works correct with @@match
  return $RegExp(re1) != re1 || $RegExp(re2) == re2 || $RegExp(re1, 'i') != '/a/i';
}))) {
  $RegExp = function RegExp(p, f) {
    var tiRE = this instanceof $RegExp;
    var piRE = isRegExp(p);
    var fiU = f === undefined;
    return !tiRE && piRE && p.constructor === $RegExp && fiU ? p
      : inheritIfRequired(CORRECT_NEW
        ? new Base(piRE && !fiU ? p.source : p, f)
        : Base((piRE = p instanceof $RegExp) ? p.source : p, piRE && fiU ? $flags.call(p) : f)
      , tiRE ? this : proto, $RegExp);
  };
  var proxy = function (key) {
    key in $RegExp || dP($RegExp, key, {
      configurable: true,
      get: function () { return Base[key]; },
      set: function (it) { Base[key] = it; }
    });
  };
  for (var keys = gOPN(Base), i = 0; keys.length > i;) proxy(keys[i++]);
  proto.constructor = $RegExp;
  $RegExp.prototype = proto;
  __webpack_require__(8859)(global, 'RegExp', $RegExp);
}

__webpack_require__(5762)('RegExp');


/***/ }),

/***/ 8305:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var anObject = __webpack_require__(4228);
var toObject = __webpack_require__(8270);
var toLength = __webpack_require__(1485);
var toInteger = __webpack_require__(7087);
var advanceStringIndex = __webpack_require__(8828);
var regExpExec = __webpack_require__(2535);
var max = Math.max;
var min = Math.min;
var floor = Math.floor;
var SUBSTITUTION_SYMBOLS = /\$([$&`']|\d\d?|<[^>]*>)/g;
var SUBSTITUTION_SYMBOLS_NO_NAMED = /\$([$&`']|\d\d?)/g;

var maybeToString = function (it) {
  return it === undefined ? it : String(it);
};

// @@replace logic
__webpack_require__(9228)('replace', 2, function (defined, REPLACE, $replace, maybeCallNative) {
  return [
    // `String.prototype.replace` method
    // https://tc39.github.io/ecma262/#sec-string.prototype.replace
    function replace(searchValue, replaceValue) {
      var O = defined(this);
      var fn = searchValue == undefined ? undefined : searchValue[REPLACE];
      return fn !== undefined
        ? fn.call(searchValue, O, replaceValue)
        : $replace.call(String(O), searchValue, replaceValue);
    },
    // `RegExp.prototype[@@replace]` method
    // https://tc39.github.io/ecma262/#sec-regexp.prototype-@@replace
    function (regexp, replaceValue) {
      var res = maybeCallNative($replace, regexp, this, replaceValue);
      if (res.done) return res.value;

      var rx = anObject(regexp);
      var S = String(this);
      var functionalReplace = typeof replaceValue === 'function';
      if (!functionalReplace) replaceValue = String(replaceValue);
      var global = rx.global;
      if (global) {
        var fullUnicode = rx.unicode;
        rx.lastIndex = 0;
      }
      var results = [];
      while (true) {
        var result = regExpExec(rx, S);
        if (result === null) break;
        results.push(result);
        if (!global) break;
        var matchStr = String(result[0]);
        if (matchStr === '') rx.lastIndex = advanceStringIndex(S, toLength(rx.lastIndex), fullUnicode);
      }
      var accumulatedResult = '';
      var nextSourcePosition = 0;
      for (var i = 0; i < results.length; i++) {
        result = results[i];
        var matched = String(result[0]);
        var position = max(min(toInteger(result.index), S.length), 0);
        var captures = [];
        // NOTE: This is equivalent to
        //   captures = result.slice(1).map(maybeToString)
        // but for some reason `nativeSlice.call(result, 1, result.length)` (called in
        // the slice polyfill when slicing native arrays) "doesn't work" in safari 9 and
        // causes a crash (https://pastebin.com/N21QzeQA) when trying to debug it.
        for (var j = 1; j < result.length; j++) captures.push(maybeToString(result[j]));
        var namedCaptures = result.groups;
        if (functionalReplace) {
          var replacerArgs = [matched].concat(captures, position, S);
          if (namedCaptures !== undefined) replacerArgs.push(namedCaptures);
          var replacement = String(replaceValue.apply(undefined, replacerArgs));
        } else {
          replacement = getSubstitution(matched, S, position, captures, namedCaptures, replaceValue);
        }
        if (position >= nextSourcePosition) {
          accumulatedResult += S.slice(nextSourcePosition, position) + replacement;
          nextSourcePosition = position + matched.length;
        }
      }
      return accumulatedResult + S.slice(nextSourcePosition);
    }
  ];

    // https://tc39.github.io/ecma262/#sec-getsubstitution
  function getSubstitution(matched, str, position, captures, namedCaptures, replacement) {
    var tailPos = position + matched.length;
    var m = captures.length;
    var symbols = SUBSTITUTION_SYMBOLS_NO_NAMED;
    if (namedCaptures !== undefined) {
      namedCaptures = toObject(namedCaptures);
      symbols = SUBSTITUTION_SYMBOLS;
    }
    return $replace.call(replacement, symbols, function (match, ch) {
      var capture;
      switch (ch.charAt(0)) {
        case '$': return '$';
        case '&': return matched;
        case '`': return str.slice(0, position);
        case "'": return str.slice(tailPos);
        case '<':
          capture = namedCaptures[ch.slice(1, -1)];
          break;
        default: // \d\d?
          var n = +ch;
          if (n === 0) return match;
          if (n > m) {
            var f = floor(n / 10);
            if (f === 0) return match;
            if (f <= m) return captures[f - 1] === undefined ? ch.charAt(1) : captures[f - 1] + ch.charAt(1);
            return match;
          }
          capture = captures[n - 1];
      }
      return capture === undefined ? '' : capture;
    });
  }
});


/***/ }),

/***/ 8423:
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

var anObject = __webpack_require__(812);
var IE8_DOM_DEFINE = __webpack_require__(2484);
var toPrimitive = __webpack_require__(752);
var dP = Object.defineProperty;

exports.f = __webpack_require__(8219) ? Object.defineProperty : function defineProperty(O, P, Attributes) {
  anObject(O);
  P = toPrimitive(P, true);
  anObject(Attributes);
  if (IE8_DOM_DEFINE) try {
    return dP(O, P, Attributes);
  } catch (e) { /* empty */ }
  if ('get' in Attributes || 'set' in Attributes) throw TypeError('Accessors not supported!');
  if ('value' in Attributes) O[P] = Attributes.value;
  return O;
};


/***/ }),

/***/ 8437:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// B.2.3.8 String.prototype.fontsize(size)
__webpack_require__(2468)('fontsize', function (createHTML) {
  return function fontsize(size) {
    return createHTML(this, 'font', 'size', size);
  };
});


/***/ }),

/***/ 8449:
/***/ (function(__unused_webpack_module, exports) {

exports.f = {}.propertyIsEnumerable;


/***/ }),

/***/ 8451:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 19.1.3.10 Object.is(value1, value2)
var $export = __webpack_require__(2127);
$export($export.S, 'Object', { is: __webpack_require__(7359) });


/***/ }),

/***/ 8535:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var global = __webpack_require__(6670);
var core = __webpack_require__(6438);
var ctx = __webpack_require__(8852);
var hide = __webpack_require__(2677);
var has = __webpack_require__(5509);
var PROTOTYPE = 'prototype';

var $export = function (type, name, source) {
  var IS_FORCED = type & $export.F;
  var IS_GLOBAL = type & $export.G;
  var IS_STATIC = type & $export.S;
  var IS_PROTO = type & $export.P;
  var IS_BIND = type & $export.B;
  var IS_WRAP = type & $export.W;
  var exports = IS_GLOBAL ? core : core[name] || (core[name] = {});
  var expProto = exports[PROTOTYPE];
  var target = IS_GLOBAL ? global : IS_STATIC ? global[name] : (global[name] || {})[PROTOTYPE];
  var key, own, out;
  if (IS_GLOBAL) source = name;
  for (key in source) {
    // contains in native
    own = !IS_FORCED && target && target[key] !== undefined;
    if (own && has(exports, key)) continue;
    // export native or passed
    out = own ? target[key] : source[key];
    // prevent global pollution for namespaces
    exports[key] = IS_GLOBAL && typeof target[key] != 'function' ? source[key]
    // bind timers to global for call from export context
    : IS_BIND && own ? ctx(out, global)
    // wrap global constructors for prevent change them in library
    : IS_WRAP && target[key] == out ? (function (C) {
      var F = function (a, b, c) {
        if (this instanceof C) {
          switch (arguments.length) {
            case 0: return new C();
            case 1: return new C(a);
            case 2: return new C(a, b);
          } return new C(a, b, c);
        } return C.apply(this, arguments);
      };
      F[PROTOTYPE] = C[PROTOTYPE];
      return F;
    // make static versions for prototype methods
    })(out) : IS_PROTO && typeof out == 'function' ? ctx(Function.call, out) : out;
    // export proto methods to core.%CONSTRUCTOR%.methods.%NAME%
    if (IS_PROTO) {
      (exports.virtual || (exports.virtual = {}))[key] = out;
      // export proto methods to core.%CONSTRUCTOR%.prototype.%NAME%
      if (type & $export.R && expProto && !expProto[key]) hide(expProto, key, out);
    }
  }
};
// type bitmap
$export.F = 1;   // forced
$export.G = 2;   // global
$export.S = 4;   // static
$export.P = 8;   // proto
$export.B = 16;  // bind
$export.W = 32;  // wrap
$export.U = 64;  // safe
$export.R = 128; // real proto method for `library`
module.exports = $export;


/***/ }),

/***/ 8537:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(7209)('Uint32', 4, function (init) {
  return function Uint32Array(data, byteOffset, length) {
    return init(this, data, byteOffset, length);
  };
});


/***/ }),

/***/ 8543:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// B.2.3.12 String.prototype.strike()
__webpack_require__(2468)('strike', function (createHTML) {
  return function strike() {
    return createHTML(this, 'strike', '', '');
  };
});


/***/ }),

/***/ 8583:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";
// https://github.com/tc39/proposal-promise-finally

var $export = __webpack_require__(2127);
var core = __webpack_require__(6094);
var global = __webpack_require__(7526);
var speciesConstructor = __webpack_require__(9190);
var promiseResolve = __webpack_require__(5957);

$export($export.P + $export.R, 'Promise', { 'finally': function (onFinally) {
  var C = speciesConstructor(this, core.Promise || global.Promise);
  var isFunction = typeof onFinally == 'function';
  return this.then(
    isFunction ? function (x) {
      return promiseResolve(C, onFinally()).then(function () { return x; });
    } : onFinally,
    isFunction ? function (e) {
      return promiseResolve(C, onFinally()).then(function () { throw e; });
    } : onFinally
  );
} });


/***/ }),

/***/ 8604:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

__webpack_require__(9638);
var anObject = __webpack_require__(4228);
var $flags = __webpack_require__(1158);
var DESCRIPTORS = __webpack_require__(1763);
var TO_STRING = 'toString';
var $toString = /./[TO_STRING];

var define = function (fn) {
  __webpack_require__(8859)(RegExp.prototype, TO_STRING, fn, true);
};

// 21.2.5.14 RegExp.prototype.toString()
if (__webpack_require__(9448)(function () { return $toString.call({ source: 'a', flags: 'b' }) != '/a/b'; })) {
  define(function toString() {
    var R = anObject(this);
    return '/'.concat(R.source, '/',
      'flags' in R ? R.flags : !DESCRIPTORS && R instanceof RegExp ? $flags.call(R) : undefined);
  });
// FF44- RegExp#toString has a wrong name
} else if ($toString.name != TO_STRING) {
  define(function toString() {
    return $toString.call(this);
  });
}


/***/ }),

/***/ 8641:
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

var pIE = __webpack_require__(8449);
var createDesc = __webpack_require__(1996);
var toIObject = __webpack_require__(7221);
var toPrimitive = __webpack_require__(3048);
var has = __webpack_require__(7917);
var IE8_DOM_DEFINE = __webpack_require__(2956);
var gOPD = Object.getOwnPropertyDescriptor;

exports.f = __webpack_require__(1763) ? gOPD : function getOwnPropertyDescriptor(O, P) {
  O = toIObject(O);
  P = toPrimitive(P, true);
  if (IE8_DOM_DEFINE) try {
    return gOPD(O, P);
  } catch (e) { /* empty */ }
  if (has(O, P)) return createDesc(!pIE.f.call(O, P), O[P]);
};


/***/ }),

/***/ 8647:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 19.1.2.14 Object.keys(O)
var toObject = __webpack_require__(8270);
var $keys = __webpack_require__(1311);

__webpack_require__(923)('keys', function () {
  return function keys(it) {
    return $keys(toObject(it));
  };
});


/***/ }),

/***/ 8699:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

__webpack_require__(7209)('Int8', 1, function (init) {
  return function Int8Array(data, byteOffset, length) {
    return init(this, data, byteOffset, length);
  };
});


/***/ }),

/***/ 8766:
/***/ (function() {

/**
 * Alert components.
 **/
const alertDismiss = document.querySelectorAll('.su-alert__dismiss-button');

// Fire when ready.
document.addEventListener('DOMContentLoaded', event => {
  // Loop through each alert with a dismiss button.
  Array.prototype.forEach.call(alertDismiss, alrt => {
    alrt.addEventListener('click', function (e) {
      // When a dismiss button is pressed. Find the nearest parent wrapper and
      // remove it all from the dom.
      e.target.closest('.su-alert').remove();
    }, false);
  });
});


/***/ }),

/***/ 8772:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// ie9- setTimeout & setInterval additional parameters fix
var global = __webpack_require__(7526);
var $export = __webpack_require__(2127);
var userAgent = __webpack_require__(4514);
var slice = [].slice;
var MSIE = /MSIE .\./.test(userAgent); // <- dirty ie9- check
var wrap = function (set) {
  return function (fn, time /* , ...args */) {
    var boundArgs = arguments.length > 2;
    var args = boundArgs ? slice.call(arguments, 2) : false;
    return set(boundArgs ? function () {
      // eslint-disable-next-line no-new-func
      (typeof fn == 'function' ? fn : Function(fn)).apply(this, args);
    } : fn, time);
  };
};
$export($export.G + $export.B + $export.F * MSIE, {
  setTimeout: wrap(global.setTimeout),
  setInterval: wrap(global.setInterval)
});


/***/ }),

/***/ 8790:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var ctx = __webpack_require__(5052);
var call = __webpack_require__(7368);
var isArrayIter = __webpack_require__(1508);
var anObject = __webpack_require__(4228);
var toLength = __webpack_require__(1485);
var getIterFn = __webpack_require__(762);
var BREAK = {};
var RETURN = {};
var exports = module.exports = function (iterable, entries, fn, that, ITERATOR) {
  var iterFn = ITERATOR ? function () { return iterable; } : getIterFn(iterable);
  var f = ctx(fn, that, entries ? 2 : 1);
  var index = 0;
  var length, step, iterator, result;
  if (typeof iterFn != 'function') throw TypeError(iterable + ' is not iterable!');
  // fast case for arrays with default iterator
  if (isArrayIter(iterFn)) for (length = toLength(iterable.length); length > index; index++) {
    result = entries ? f(anObject(step = iterable[index])[0], step[1]) : f(iterable[index]);
    if (result === BREAK || result === RETURN) return result;
  } else for (iterator = iterFn.call(iterable); !(step = iterator.next()).done;) {
    result = call(iterator, f, step.value, entries);
    if (result === BREAK || result === RETURN) return result;
  }
};
exports.BREAK = BREAK;
exports.RETURN = RETURN;


/***/ }),

/***/ 8828:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var at = __webpack_require__(1212)(true);

 // `AdvanceStringIndex` abstract operation
// https://tc39.github.io/ecma262/#sec-advancestringindex
module.exports = function (S, index, unicode) {
  return index + (unicode ? at(S, index).length : 1);
};


/***/ }),

/***/ 8852:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// optional / simple context binding
var aFunction = __webpack_require__(5219);
module.exports = function (fn, that, length) {
  aFunction(fn);
  if (that === undefined) return fn;
  switch (length) {
    case 1: return function (a) {
      return fn.call(that, a);
    };
    case 2: return function (a, b) {
      return fn.call(that, a, b);
    };
    case 3: return function (a, b, c) {
      return fn.call(that, a, b, c);
    };
  }
  return function (/* ...args */) {
    return fn.apply(that, arguments);
  };
};


/***/ }),

/***/ 8859:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var global = __webpack_require__(7526);
var hide = __webpack_require__(3341);
var has = __webpack_require__(7917);
var SRC = __webpack_require__(4415)('src');
var $toString = __webpack_require__(9461);
var TO_STRING = 'toString';
var TPL = ('' + $toString).split(TO_STRING);

(__webpack_require__(6094).inspectSource) = function (it) {
  return $toString.call(it);
};

(module.exports = function (O, key, val, safe) {
  var isFunction = typeof val == 'function';
  if (isFunction) has(val, 'name') || hide(val, 'name', key);
  if (O[key] === val) return;
  if (isFunction) has(val, SRC) || hide(val, SRC, O[key] ? '' + O[key] : TPL.join(String(key)));
  if (O === global) {
    O[key] = val;
  } else if (!safe) {
    delete O[key];
    hide(O, key, val);
  } else if (O[key]) {
    O[key] = val;
  } else {
    hide(O, key, val);
  }
// add fake Function#toString for correct work wrapped methods / constructors with methods like LoDash isNative
})(Function.prototype, TO_STRING, function toString() {
  return typeof this == 'function' && this[SRC] || $toString.call(this);
});


/***/ }),

/***/ 8872:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";
// 21.1.3.7 String.prototype.includes(searchString, position = 0)

var $export = __webpack_require__(2127);
var context = __webpack_require__(8942);
var INCLUDES = 'includes';

$export($export.P + $export.F * __webpack_require__(5203)(INCLUDES), 'String', {
  includes: function includes(searchString /* , position = 0 */) {
    return !!~context(this, searchString, INCLUDES)
      .indexOf(searchString, arguments.length > 1 ? arguments[1] : undefined);
  }
});


/***/ }),

/***/ 8880:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var isObject = __webpack_require__(3305);
var setPrototypeOf = (__webpack_require__(5170).set);
module.exports = function (that, target, C) {
  var S = target.constructor;
  var P;
  if (S !== C && typeof S == 'function' && (P = S.prototype) !== C.prototype && isObject(P) && setPrototypeOf) {
    setPrototypeOf(that, P);
  } return that;
};


/***/ }),

/***/ 8888:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var $export = __webpack_require__(2127);
var $every = __webpack_require__(6179)(4);

$export($export.P + $export.F * !__webpack_require__(6884)([].every, true), 'Array', {
  // 22.1.3.5 / 15.4.4.16 Array.prototype.every(callbackfn [, thisArg])
  every: function every(callbackfn /* , thisArg */) {
    return $every(this, callbackfn, arguments[1]);
  }
});


/***/ }),

/***/ 8892:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var $export = __webpack_require__(2127);
var $some = __webpack_require__(6179)(3);

$export($export.P + $export.F * !__webpack_require__(6884)([].some, true), 'Array', {
  // 22.1.3.23 / 15.4.4.17 Array.prototype.some(callbackfn [, thisArg])
  some: function some(callbackfn /* , thisArg */) {
    return $some(this, callbackfn, arguments[1]);
  }
});


/***/ }),

/***/ 8931:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var ITERATOR = __webpack_require__(7574)('iterator');
var SAFE_CLOSING = false;

try {
  var riter = [7][ITERATOR]();
  riter['return'] = function () { SAFE_CLOSING = true; };
  // eslint-disable-next-line no-throw-literal
  Array.from(riter, function () { throw 2; });
} catch (e) { /* empty */ }

module.exports = function (exec, skipClosing) {
  if (!skipClosing && !SAFE_CLOSING) return false;
  var safe = false;
  try {
    var arr = [7];
    var iter = arr[ITERATOR]();
    iter.next = function () { return { done: safe = true }; };
    arr[ITERATOR] = function () { return iter; };
    exec(arr);
  } catch (e) { /* empty */ }
  return safe;
};


/***/ }),

/***/ 8933:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var global = __webpack_require__(7526);
var $export = __webpack_require__(2127);
var redefine = __webpack_require__(8859);
var redefineAll = __webpack_require__(6065);
var meta = __webpack_require__(2988);
var forOf = __webpack_require__(8790);
var anInstance = __webpack_require__(6440);
var isObject = __webpack_require__(3305);
var fails = __webpack_require__(9448);
var $iterDetect = __webpack_require__(8931);
var setToStringTag = __webpack_require__(3844);
var inheritIfRequired = __webpack_require__(8880);

module.exports = function (NAME, wrapper, methods, common, IS_MAP, IS_WEAK) {
  var Base = global[NAME];
  var C = Base;
  var ADDER = IS_MAP ? 'set' : 'add';
  var proto = C && C.prototype;
  var O = {};
  var fixMethod = function (KEY) {
    var fn = proto[KEY];
    redefine(proto, KEY,
      KEY == 'delete' ? function (a) {
        return IS_WEAK && !isObject(a) ? false : fn.call(this, a === 0 ? 0 : a);
      } : KEY == 'has' ? function has(a) {
        return IS_WEAK && !isObject(a) ? false : fn.call(this, a === 0 ? 0 : a);
      } : KEY == 'get' ? function get(a) {
        return IS_WEAK && !isObject(a) ? undefined : fn.call(this, a === 0 ? 0 : a);
      } : KEY == 'add' ? function add(a) { fn.call(this, a === 0 ? 0 : a); return this; }
        : function set(a, b) { fn.call(this, a === 0 ? 0 : a, b); return this; }
    );
  };
  if (typeof C != 'function' || !(IS_WEAK || proto.forEach && !fails(function () {
    new C().entries().next();
  }))) {
    // create collection constructor
    C = common.getConstructor(wrapper, NAME, IS_MAP, ADDER);
    redefineAll(C.prototype, methods);
    meta.NEED = true;
  } else {
    var instance = new C();
    // early implementations not supports chaining
    var HASNT_CHAINING = instance[ADDER](IS_WEAK ? {} : -0, 1) != instance;
    // V8 ~  Chromium 40- weak-collections throws on primitives, but should return false
    var THROWS_ON_PRIMITIVES = fails(function () { instance.has(1); });
    // most early implementations doesn't supports iterables, most modern - not close it correctly
    var ACCEPT_ITERABLES = $iterDetect(function (iter) { new C(iter); }); // eslint-disable-line no-new
    // for early implementations -0 and +0 not the same
    var BUGGY_ZERO = !IS_WEAK && fails(function () {
      // V8 ~ Chromium 42- fails only with 5+ elements
      var $instance = new C();
      var index = 5;
      while (index--) $instance[ADDER](index, index);
      return !$instance.has(-0);
    });
    if (!ACCEPT_ITERABLES) {
      C = wrapper(function (target, iterable) {
        anInstance(target, C, NAME);
        var that = inheritIfRequired(new Base(), target, C);
        if (iterable != undefined) forOf(iterable, IS_MAP, that[ADDER], that);
        return that;
      });
      C.prototype = proto;
      proto.constructor = C;
    }
    if (THROWS_ON_PRIMITIVES || BUGGY_ZERO) {
      fixMethod('delete');
      fixMethod('has');
      IS_MAP && fixMethod('get');
    }
    if (BUGGY_ZERO || HASNT_CHAINING) fixMethod(ADDER);
    // weak collections should not contains .clear method
    if (IS_WEAK && proto.clear) delete proto.clear;
  }

  setToStringTag(C, NAME);

  O[NAME] = C;
  $export($export.G + $export.W + $export.F * (C != Base), O);

  if (!IS_WEAK) common.setStrong(C, NAME, IS_MAP);

  return C;
};


/***/ }),

/***/ 8942:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// helper for String#{startsWith, endsWith, includes}
var isRegExp = __webpack_require__(5411);
var defined = __webpack_require__(3344);

module.exports = function (that, searchString, NAME) {
  if (isRegExp(searchString)) throw TypeError('String#' + NAME + " doesn't accept regex!");
  return String(defined(that));
};


/***/ }),

/***/ 8951:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

var TO_PRIMITIVE = __webpack_require__(7574)('toPrimitive');
var proto = Date.prototype;

if (!(TO_PRIMITIVE in proto)) __webpack_require__(3341)(proto, TO_PRIMITIVE, __webpack_require__(107));


/***/ }),

/***/ 8978:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

__webpack_require__(6517);
__webpack_require__(8583);
module.exports = __webpack_require__(6094).Promise["finally"];


/***/ }),

/***/ 9011:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// B.2.3.3 String.prototype.big()
__webpack_require__(2468)('big', function (createHTML) {
  return function big() {
    return createHTML(this, 'big', '', '');
  };
});


/***/ }),

/***/ 9073:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 19.1.2.11 Object.isExtensible(O)
var isObject = __webpack_require__(3305);

__webpack_require__(923)('isExtensible', function ($isExtensible) {
  return function isExtensible(it) {
    return isObject(it) ? $isExtensible ? $isExtensible(it) : true : false;
  };
});


/***/ }),

/***/ 9087:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// https://github.com/tc39/Array.prototype.includes
var $export = __webpack_require__(2127);
var $includes = __webpack_require__(1464)(true);

$export($export.P, 'Array', {
  includes: function includes(el /* , fromIndex = 0 */) {
    return $includes(this, el, arguments.length > 1 ? arguments[1] : undefined);
  }
});

__webpack_require__(8184)('includes');


/***/ }),

/***/ 9134:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.2.2.30 Math.sinh(x)
var $export = __webpack_require__(2127);
var expm1 = __webpack_require__(5551);
var exp = Math.exp;

// V8 near Chromium 38 has a problem with very small numbers
$export($export.S + $export.F * __webpack_require__(9448)(function () {
  return !Math.sinh(-2e-17) != -2e-17;
}), 'Math', {
  sinh: function sinh(x) {
    return Math.abs(x = +x) < 1
      ? (expm1(x) - expm1(-x)) / 2
      : (exp(x - 1) - exp(-x - 1)) * (Math.E / 2);
  }
});


/***/ }),

/***/ 9147:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.2.2.14 Math.expm1(x)
var $export = __webpack_require__(2127);
var $expm1 = __webpack_require__(5551);

$export($export.S + $export.F * ($expm1 != Math.expm1), 'Math', { expm1: $expm1 });


/***/ }),

/***/ 9190:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// 7.3.20 SpeciesConstructor(O, defaultConstructor)
var anObject = __webpack_require__(4228);
var aFunction = __webpack_require__(3387);
var SPECIES = __webpack_require__(7574)('species');
module.exports = function (O, D) {
  var C = anObject(O).constructor;
  var S;
  return C === undefined || (S = anObject(C)[SPECIES]) == undefined ? D : aFunction(S);
};


/***/ }),

/***/ 9213:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// B.2.3.7 String.prototype.fontcolor(color)
__webpack_require__(2468)('fontcolor', function (createHTML) {
  return function fontcolor(color) {
    return createHTML(this, 'font', 'color', color);
  };
});


/***/ }),

/***/ 9228:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

__webpack_require__(4116);
var redefine = __webpack_require__(8859);
var hide = __webpack_require__(3341);
var fails = __webpack_require__(9448);
var defined = __webpack_require__(3344);
var wks = __webpack_require__(7574);
var regexpExec = __webpack_require__(9600);

var SPECIES = wks('species');

var REPLACE_SUPPORTS_NAMED_GROUPS = !fails(function () {
  // #replace needs built-in support for named groups.
  // #match works fine because it just return the exec results, even if it has
  // a "grops" property.
  var re = /./;
  re.exec = function () {
    var result = [];
    result.groups = { a: '7' };
    return result;
  };
  return ''.replace(re, '$<a>') !== '7';
});

var SPLIT_WORKS_WITH_OVERWRITTEN_EXEC = (function () {
  // Chrome 51 has a buggy "split" implementation when RegExp#exec !== nativeExec
  var re = /(?:)/;
  var originalExec = re.exec;
  re.exec = function () { return originalExec.apply(this, arguments); };
  var result = 'ab'.split(re);
  return result.length === 2 && result[0] === 'a' && result[1] === 'b';
})();

module.exports = function (KEY, length, exec) {
  var SYMBOL = wks(KEY);

  var DELEGATES_TO_SYMBOL = !fails(function () {
    // String methods call symbol-named RegEp methods
    var O = {};
    O[SYMBOL] = function () { return 7; };
    return ''[KEY](O) != 7;
  });

  var DELEGATES_TO_EXEC = DELEGATES_TO_SYMBOL ? !fails(function () {
    // Symbol-named RegExp methods call .exec
    var execCalled = false;
    var re = /a/;
    re.exec = function () { execCalled = true; return null; };
    if (KEY === 'split') {
      // RegExp[@@split] doesn't call the regex's exec method, but first creates
      // a new one. We need to return the patched regex when creating the new one.
      re.constructor = {};
      re.constructor[SPECIES] = function () { return re; };
    }
    re[SYMBOL]('');
    return !execCalled;
  }) : undefined;

  if (
    !DELEGATES_TO_SYMBOL ||
    !DELEGATES_TO_EXEC ||
    (KEY === 'replace' && !REPLACE_SUPPORTS_NAMED_GROUPS) ||
    (KEY === 'split' && !SPLIT_WORKS_WITH_OVERWRITTEN_EXEC)
  ) {
    var nativeRegExpMethod = /./[SYMBOL];
    var fns = exec(
      defined,
      SYMBOL,
      ''[KEY],
      function maybeCallNative(nativeMethod, regexp, str, arg2, forceStringMethod) {
        if (regexp.exec === regexpExec) {
          if (DELEGATES_TO_SYMBOL && !forceStringMethod) {
            // The native String method already delegates to @@method (this
            // polyfilled function), leasing to infinite recursion.
            // We avoid it by directly calling the native @@method method.
            return { done: true, value: nativeRegExpMethod.call(regexp, str, arg2) };
          }
          return { done: true, value: nativeMethod.call(str, regexp, arg2) };
        }
        return { done: false };
      }
    );
    var strfn = fns[0];
    var rxfn = fns[1];

    redefine(String.prototype, KEY, strfn);
    hide(RegExp.prototype, SYMBOL, length == 2
      // 21.2.5.8 RegExp.prototype[@@replace](string, replaceValue)
      // 21.2.5.11 RegExp.prototype[@@split](string, limit)
      ? function (string, arg) { return rxfn.call(string, this, arg); }
      // 21.2.5.6 RegExp.prototype[@@match](string)
      // 21.2.5.9 RegExp.prototype[@@search](string)
      : function (string) { return rxfn.call(string, this); }
    );
  }
};


/***/ }),

/***/ 9318:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 19.1.2.12 Object.isFrozen(O)
var isObject = __webpack_require__(3305);

__webpack_require__(923)('isFrozen', function ($isFrozen) {
  return function isFrozen(it) {
    return isObject(it) ? $isFrozen ? $isFrozen(it) : false : true;
  };
});


/***/ }),

/***/ 9397:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var global = __webpack_require__(7526);
var each = __webpack_require__(6179)(0);
var redefine = __webpack_require__(8859);
var meta = __webpack_require__(2988);
var assign = __webpack_require__(8206);
var weak = __webpack_require__(9882);
var isObject = __webpack_require__(3305);
var validate = __webpack_require__(2888);
var NATIVE_WEAK_MAP = __webpack_require__(2888);
var IS_IE11 = !global.ActiveXObject && 'ActiveXObject' in global;
var WEAK_MAP = 'WeakMap';
var getWeak = meta.getWeak;
var isExtensible = Object.isExtensible;
var uncaughtFrozenStore = weak.ufstore;
var InternalMap;

var wrapper = function (get) {
  return function WeakMap() {
    return get(this, arguments.length > 0 ? arguments[0] : undefined);
  };
};

var methods = {
  // 23.3.3.3 WeakMap.prototype.get(key)
  get: function get(key) {
    if (isObject(key)) {
      var data = getWeak(key);
      if (data === true) return uncaughtFrozenStore(validate(this, WEAK_MAP)).get(key);
      return data ? data[this._i] : undefined;
    }
  },
  // 23.3.3.5 WeakMap.prototype.set(key, value)
  set: function set(key, value) {
    return weak.def(validate(this, WEAK_MAP), key, value);
  }
};

// 23.3 WeakMap Objects
var $WeakMap = module.exports = __webpack_require__(8933)(WEAK_MAP, wrapper, methods, weak, true, true);

// IE11 WeakMap frozen keys fix
if (NATIVE_WEAK_MAP && IS_IE11) {
  InternalMap = weak.getConstructor(wrapper, WEAK_MAP);
  assign(InternalMap.prototype, methods);
  meta.NEED = true;
  each(['delete', 'has', 'get', 'set'], function (key) {
    var proto = $WeakMap.prototype;
    var method = proto[key];
    redefine(proto, key, function (a, b) {
      // store frozen objects on internal weakmap shim
      if (isObject(a) && !isExtensible(a)) {
        if (!this._f) this._f = new InternalMap();
        var result = this._f[key](a, b);
        return key == 'set' ? this : result;
      // store all the rest on native weakmap
      } return method.call(this, a, b);
    });
  });
}


/***/ }),

/***/ 9415:
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

// 19.1.2.7 / 15.2.3.4 Object.getOwnPropertyNames(O)
var $keys = __webpack_require__(4561);
var hiddenKeys = (__webpack_require__(6140).concat)('length', 'prototype');

exports.f = Object.getOwnPropertyNames || function getOwnPropertyNames(O) {
  return $keys(O, hiddenKeys);
};


/***/ }),

/***/ 9429:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.3.4.36 / 15.9.5.43 Date.prototype.toISOString()
var $export = __webpack_require__(2127);
var toISOString = __webpack_require__(5385);

// PhantomJS / old WebKit has a broken implementations
$export($export.P + $export.F * (Date.prototype.toISOString !== toISOString), 'Date', {
  toISOString: toISOString
});


/***/ }),

/***/ 9448:
/***/ (function(module) {

module.exports = function (exec) {
  try {
    return !!exec();
  } catch (e) {
    return true;
  }
};


/***/ }),

/***/ 9461:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

module.exports = __webpack_require__(4556)('native-function-to-string', Function.toString);


/***/ }),

/***/ 9491:
/***/ (function(module) {

"use strict";
/**
 * Code refactored from Mozilla Developer Network:
 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/assign
 */



function assign(target, firstSource) {
  if (target === undefined || target === null) {
    throw new TypeError('Cannot convert first argument to object');
  }

  var to = Object(target);
  for (var i = 1; i < arguments.length; i++) {
    var nextSource = arguments[i];
    if (nextSource === undefined || nextSource === null) {
      continue;
    }

    var keysArray = Object.keys(Object(nextSource));
    for (var nextIndex = 0, len = keysArray.length; nextIndex < len; nextIndex++) {
      var nextKey = keysArray[nextIndex];
      var desc = Object.getOwnPropertyDescriptor(nextSource, nextKey);
      if (desc !== undefined && desc.enumerable) {
        to[nextKey] = nextSource[nextKey];
      }
    }
  }
  return to;
}

function polyfill() {
  if (!Object.assign) {
    Object.defineProperty(Object, 'assign', {
      enumerable: false,
      configurable: true,
      writable: true,
      value: assign
    });
  }
}

module.exports = {
  assign: assign,
  polyfill: polyfill
};


/***/ }),

/***/ 9497:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.1.2.4 Number.isNaN(number)
var $export = __webpack_require__(2127);

$export($export.S, 'Number', {
  isNaN: function isNaN(number) {
    // eslint-disable-next-line no-self-compare
    return number != number;
  }
});


/***/ }),

/***/ 9584:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 20.2.2.22 Math.log2(x)
var $export = __webpack_require__(2127);

$export($export.S, 'Math', {
  log2: function log2(x) {
    return Math.log(x) / Math.LN2;
  }
});


/***/ }),

/***/ 9600:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var regexpFlags = __webpack_require__(1158);

var nativeExec = RegExp.prototype.exec;
// This always refers to the native implementation, because the
// String#replace polyfill uses ./fix-regexp-well-known-symbol-logic.js,
// which loads this file before patching the method.
var nativeReplace = String.prototype.replace;

var patchedExec = nativeExec;

var LAST_INDEX = 'lastIndex';

var UPDATES_LAST_INDEX_WRONG = (function () {
  var re1 = /a/,
      re2 = /b*/g;
  nativeExec.call(re1, 'a');
  nativeExec.call(re2, 'a');
  return re1[LAST_INDEX] !== 0 || re2[LAST_INDEX] !== 0;
})();

// nonparticipating capturing group, copied from es5-shim's String#split patch.
var NPCG_INCLUDED = /()??/.exec('')[1] !== undefined;

var PATCH = UPDATES_LAST_INDEX_WRONG || NPCG_INCLUDED;

if (PATCH) {
  patchedExec = function exec(str) {
    var re = this;
    var lastIndex, reCopy, match, i;

    if (NPCG_INCLUDED) {
      reCopy = new RegExp('^' + re.source + '$(?!\\s)', regexpFlags.call(re));
    }
    if (UPDATES_LAST_INDEX_WRONG) lastIndex = re[LAST_INDEX];

    match = nativeExec.call(re, str);

    if (UPDATES_LAST_INDEX_WRONG && match) {
      re[LAST_INDEX] = re.global ? match.index + match[0].length : lastIndex;
    }
    if (NPCG_INCLUDED && match && match.length > 1) {
      // Fix browsers whose `exec` methods don't consistently return `undefined`
      // for NPCG, like IE8. NOTE: This doesn' work for /(.?)?/
      // eslint-disable-next-line no-loop-func
      nativeReplace.call(match[0], reCopy, function () {
        for (i = 1; i < arguments.length - 2; i++) {
          if (arguments[i] === undefined) match[i] = undefined;
        }
      });
    }

    return match;
  };
}

module.exports = patchedExec;


/***/ }),

/***/ 9620:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 22.1.3.3 Array.prototype.copyWithin(target, start, end = this.length)
var $export = __webpack_require__(2127);

$export($export.P, 'Array', { copyWithin: __webpack_require__(4438) });

__webpack_require__(8184)('copyWithin');


/***/ }),

/***/ 9638:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

// 21.2.5.3 get RegExp.prototype.flags()
if (__webpack_require__(1763) && /./g.flags != 'g') (__webpack_require__(7967).f)(RegExp.prototype, 'flags', {
  configurable: true,
  get: __webpack_require__(1158)
});


/***/ }),

/***/ 9650:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// ECMAScript 6 symbols shim
var global = __webpack_require__(7526);
var has = __webpack_require__(7917);
var DESCRIPTORS = __webpack_require__(1763);
var $export = __webpack_require__(2127);
var redefine = __webpack_require__(8859);
var META = (__webpack_require__(2988).KEY);
var $fails = __webpack_require__(9448);
var shared = __webpack_require__(4556);
var setToStringTag = __webpack_require__(3844);
var uid = __webpack_require__(4415);
var wks = __webpack_require__(7574);
var wksExt = __webpack_require__(7960);
var wksDefine = __webpack_require__(5392);
var enumKeys = __webpack_require__(5969);
var isArray = __webpack_require__(7981);
var anObject = __webpack_require__(4228);
var isObject = __webpack_require__(3305);
var toObject = __webpack_require__(8270);
var toIObject = __webpack_require__(7221);
var toPrimitive = __webpack_require__(3048);
var createDesc = __webpack_require__(1996);
var _create = __webpack_require__(4719);
var gOPNExt = __webpack_require__(4765);
var $GOPD = __webpack_require__(8641);
var $GOPS = __webpack_require__(1060);
var $DP = __webpack_require__(7967);
var $keys = __webpack_require__(1311);
var gOPD = $GOPD.f;
var dP = $DP.f;
var gOPN = gOPNExt.f;
var $Symbol = global.Symbol;
var $JSON = global.JSON;
var _stringify = $JSON && $JSON.stringify;
var PROTOTYPE = 'prototype';
var HIDDEN = wks('_hidden');
var TO_PRIMITIVE = wks('toPrimitive');
var isEnum = {}.propertyIsEnumerable;
var SymbolRegistry = shared('symbol-registry');
var AllSymbols = shared('symbols');
var OPSymbols = shared('op-symbols');
var ObjectProto = Object[PROTOTYPE];
var USE_NATIVE = typeof $Symbol == 'function' && !!$GOPS.f;
var QObject = global.QObject;
// Don't use setters in Qt Script, https://github.com/zloirock/core-js/issues/173
var setter = !QObject || !QObject[PROTOTYPE] || !QObject[PROTOTYPE].findChild;

// fallback for old Android, https://code.google.com/p/v8/issues/detail?id=687
var setSymbolDesc = DESCRIPTORS && $fails(function () {
  return _create(dP({}, 'a', {
    get: function () { return dP(this, 'a', { value: 7 }).a; }
  })).a != 7;
}) ? function (it, key, D) {
  var protoDesc = gOPD(ObjectProto, key);
  if (protoDesc) delete ObjectProto[key];
  dP(it, key, D);
  if (protoDesc && it !== ObjectProto) dP(ObjectProto, key, protoDesc);
} : dP;

var wrap = function (tag) {
  var sym = AllSymbols[tag] = _create($Symbol[PROTOTYPE]);
  sym._k = tag;
  return sym;
};

var isSymbol = USE_NATIVE && typeof $Symbol.iterator == 'symbol' ? function (it) {
  return typeof it == 'symbol';
} : function (it) {
  return it instanceof $Symbol;
};

var $defineProperty = function defineProperty(it, key, D) {
  if (it === ObjectProto) $defineProperty(OPSymbols, key, D);
  anObject(it);
  key = toPrimitive(key, true);
  anObject(D);
  if (has(AllSymbols, key)) {
    if (!D.enumerable) {
      if (!has(it, HIDDEN)) dP(it, HIDDEN, createDesc(1, {}));
      it[HIDDEN][key] = true;
    } else {
      if (has(it, HIDDEN) && it[HIDDEN][key]) it[HIDDEN][key] = false;
      D = _create(D, { enumerable: createDesc(0, false) });
    } return setSymbolDesc(it, key, D);
  } return dP(it, key, D);
};
var $defineProperties = function defineProperties(it, P) {
  anObject(it);
  var keys = enumKeys(P = toIObject(P));
  var i = 0;
  var l = keys.length;
  var key;
  while (l > i) $defineProperty(it, key = keys[i++], P[key]);
  return it;
};
var $create = function create(it, P) {
  return P === undefined ? _create(it) : $defineProperties(_create(it), P);
};
var $propertyIsEnumerable = function propertyIsEnumerable(key) {
  var E = isEnum.call(this, key = toPrimitive(key, true));
  if (this === ObjectProto && has(AllSymbols, key) && !has(OPSymbols, key)) return false;
  return E || !has(this, key) || !has(AllSymbols, key) || has(this, HIDDEN) && this[HIDDEN][key] ? E : true;
};
var $getOwnPropertyDescriptor = function getOwnPropertyDescriptor(it, key) {
  it = toIObject(it);
  key = toPrimitive(key, true);
  if (it === ObjectProto && has(AllSymbols, key) && !has(OPSymbols, key)) return;
  var D = gOPD(it, key);
  if (D && has(AllSymbols, key) && !(has(it, HIDDEN) && it[HIDDEN][key])) D.enumerable = true;
  return D;
};
var $getOwnPropertyNames = function getOwnPropertyNames(it) {
  var names = gOPN(toIObject(it));
  var result = [];
  var i = 0;
  var key;
  while (names.length > i) {
    if (!has(AllSymbols, key = names[i++]) && key != HIDDEN && key != META) result.push(key);
  } return result;
};
var $getOwnPropertySymbols = function getOwnPropertySymbols(it) {
  var IS_OP = it === ObjectProto;
  var names = gOPN(IS_OP ? OPSymbols : toIObject(it));
  var result = [];
  var i = 0;
  var key;
  while (names.length > i) {
    if (has(AllSymbols, key = names[i++]) && (IS_OP ? has(ObjectProto, key) : true)) result.push(AllSymbols[key]);
  } return result;
};

// 19.4.1.1 Symbol([description])
if (!USE_NATIVE) {
  $Symbol = function Symbol() {
    if (this instanceof $Symbol) throw TypeError('Symbol is not a constructor!');
    var tag = uid(arguments.length > 0 ? arguments[0] : undefined);
    var $set = function (value) {
      if (this === ObjectProto) $set.call(OPSymbols, value);
      if (has(this, HIDDEN) && has(this[HIDDEN], tag)) this[HIDDEN][tag] = false;
      setSymbolDesc(this, tag, createDesc(1, value));
    };
    if (DESCRIPTORS && setter) setSymbolDesc(ObjectProto, tag, { configurable: true, set: $set });
    return wrap(tag);
  };
  redefine($Symbol[PROTOTYPE], 'toString', function toString() {
    return this._k;
  });

  $GOPD.f = $getOwnPropertyDescriptor;
  $DP.f = $defineProperty;
  (__webpack_require__(9415).f) = gOPNExt.f = $getOwnPropertyNames;
  (__webpack_require__(8449).f) = $propertyIsEnumerable;
  $GOPS.f = $getOwnPropertySymbols;

  if (DESCRIPTORS && !__webpack_require__(2750)) {
    redefine(ObjectProto, 'propertyIsEnumerable', $propertyIsEnumerable, true);
  }

  wksExt.f = function (name) {
    return wrap(wks(name));
  };
}

$export($export.G + $export.W + $export.F * !USE_NATIVE, { Symbol: $Symbol });

for (var es6Symbols = (
  // 19.4.2.2, 19.4.2.3, 19.4.2.4, 19.4.2.6, 19.4.2.8, 19.4.2.9, 19.4.2.10, 19.4.2.11, 19.4.2.12, 19.4.2.13, 19.4.2.14
  'hasInstance,isConcatSpreadable,iterator,match,replace,search,species,split,toPrimitive,toStringTag,unscopables'
).split(','), j = 0; es6Symbols.length > j;)wks(es6Symbols[j++]);

for (var wellKnownSymbols = $keys(wks.store), k = 0; wellKnownSymbols.length > k;) wksDefine(wellKnownSymbols[k++]);

$export($export.S + $export.F * !USE_NATIVE, 'Symbol', {
  // 19.4.2.1 Symbol.for(key)
  'for': function (key) {
    return has(SymbolRegistry, key += '')
      ? SymbolRegistry[key]
      : SymbolRegistry[key] = $Symbol(key);
  },
  // 19.4.2.5 Symbol.keyFor(sym)
  keyFor: function keyFor(sym) {
    if (!isSymbol(sym)) throw TypeError(sym + ' is not a symbol!');
    for (var key in SymbolRegistry) if (SymbolRegistry[key] === sym) return key;
  },
  useSetter: function () { setter = true; },
  useSimple: function () { setter = false; }
});

$export($export.S + $export.F * !USE_NATIVE, 'Object', {
  // 19.1.2.2 Object.create(O [, Properties])
  create: $create,
  // 19.1.2.4 Object.defineProperty(O, P, Attributes)
  defineProperty: $defineProperty,
  // 19.1.2.3 Object.defineProperties(O, Properties)
  defineProperties: $defineProperties,
  // 19.1.2.6 Object.getOwnPropertyDescriptor(O, P)
  getOwnPropertyDescriptor: $getOwnPropertyDescriptor,
  // 19.1.2.7 Object.getOwnPropertyNames(O)
  getOwnPropertyNames: $getOwnPropertyNames,
  // 19.1.2.8 Object.getOwnPropertySymbols(O)
  getOwnPropertySymbols: $getOwnPropertySymbols
});

// Chrome 38 and 39 `Object.getOwnPropertySymbols` fails on primitives
// https://bugs.chromium.org/p/v8/issues/detail?id=3443
var FAILS_ON_PRIMITIVES = $fails(function () { $GOPS.f(1); });

$export($export.S + $export.F * FAILS_ON_PRIMITIVES, 'Object', {
  getOwnPropertySymbols: function getOwnPropertySymbols(it) {
    return $GOPS.f(toObject(it));
  }
});

// 24.3.2 JSON.stringify(value [, replacer [, space]])
$JSON && $export($export.S + $export.F * (!USE_NATIVE || $fails(function () {
  var S = $Symbol();
  // MS Edge converts symbol values to JSON as {}
  // WebKit converts symbol values to JSON as null
  // V8 throws on boxed symbols
  return _stringify([S]) != '[null]' || _stringify({ a: S }) != '{}' || _stringify(Object(S)) != '{}';
})), 'JSON', {
  stringify: function stringify(it) {
    var args = [it];
    var i = 1;
    var replacer, $replacer;
    while (arguments.length > i) args.push(arguments[i++]);
    $replacer = replacer = args[1];
    if (!isObject(replacer) && it === undefined || isSymbol(it)) return; // IE8 returns string on undefined
    if (!isArray(replacer)) replacer = function (key, value) {
      if (typeof $replacer == 'function') value = $replacer.call(this, key, value);
      if (!isSymbol(value)) return value;
    };
    args[1] = replacer;
    return _stringify.apply($JSON, args);
  }
});

// 19.4.3.4 Symbol.prototype[@@toPrimitive](hint)
$Symbol[PROTOTYPE][TO_PRIMITIVE] || __webpack_require__(3341)($Symbol[PROTOTYPE], TO_PRIMITIVE, $Symbol[PROTOTYPE].valueOf);
// 19.4.3.5 Symbol.prototype[@@toStringTag]
setToStringTag($Symbol, 'Symbol');
// 20.2.1.9 Math[@@toStringTag]
setToStringTag(Math, 'Math', true);
// 24.3.3 JSON[@@toStringTag]
setToStringTag(global.JSON, 'JSON', true);


/***/ }),

/***/ 9766:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// https://tc39.github.io/proposal-flatMap/#sec-Array.prototype.flatMap
var $export = __webpack_require__(2127);
var flattenIntoArray = __webpack_require__(2322);
var toObject = __webpack_require__(8270);
var toLength = __webpack_require__(1485);
var aFunction = __webpack_require__(3387);
var arraySpeciesCreate = __webpack_require__(3191);

$export($export.P, 'Array', {
  flatMap: function flatMap(callbackfn /* , thisArg */) {
    var O = toObject(this);
    var sourceLen, A;
    aFunction(callbackfn);
    sourceLen = toLength(O.length);
    A = arraySpeciesCreate(O, 0);
    flattenIntoArray(A, O, O, sourceLen, 0, 1, callbackfn, arguments[1]);
    return A;
  }
});

__webpack_require__(8184)('flatMap');


/***/ }),

/***/ 9813:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var $export = __webpack_require__(2127);
var $filter = __webpack_require__(6179)(2);

$export($export.P + $export.F * !__webpack_require__(6884)([].filter, true), 'Array', {
  // 22.1.3.7 / 15.4.4.20 Array.prototype.filter(callbackfn [, thisArg])
  filter: function filter(callbackfn /* , thisArg */) {
    return $filter(this, callbackfn, arguments[1]);
  }
});


/***/ }),

/***/ 9839:
/***/ (function(__unused_webpack_module, __unused_webpack_exports, __webpack_require__) {

"use strict";

// B.2.3.9 String.prototype.italics()
__webpack_require__(2468)('italics', function (createHTML) {
  return function italics() {
    return createHTML(this, 'i', '', '');
  };
});


/***/ }),

/***/ 9882:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";

var redefineAll = __webpack_require__(6065);
var getWeak = (__webpack_require__(2988).getWeak);
var anObject = __webpack_require__(4228);
var isObject = __webpack_require__(3305);
var anInstance = __webpack_require__(6440);
var forOf = __webpack_require__(8790);
var createArrayMethod = __webpack_require__(6179);
var $has = __webpack_require__(7917);
var validate = __webpack_require__(2888);
var arrayFind = createArrayMethod(5);
var arrayFindIndex = createArrayMethod(6);
var id = 0;

// fallback for uncaught frozen keys
var uncaughtFrozenStore = function (that) {
  return that._l || (that._l = new UncaughtFrozenStore());
};
var UncaughtFrozenStore = function () {
  this.a = [];
};
var findUncaughtFrozen = function (store, key) {
  return arrayFind(store.a, function (it) {
    return it[0] === key;
  });
};
UncaughtFrozenStore.prototype = {
  get: function (key) {
    var entry = findUncaughtFrozen(this, key);
    if (entry) return entry[1];
  },
  has: function (key) {
    return !!findUncaughtFrozen(this, key);
  },
  set: function (key, value) {
    var entry = findUncaughtFrozen(this, key);
    if (entry) entry[1] = value;
    else this.a.push([key, value]);
  },
  'delete': function (key) {
    var index = arrayFindIndex(this.a, function (it) {
      return it[0] === key;
    });
    if (~index) this.a.splice(index, 1);
    return !!~index;
  }
};

module.exports = {
  getConstructor: function (wrapper, NAME, IS_MAP, ADDER) {
    var C = wrapper(function (that, iterable) {
      anInstance(that, C, NAME, '_i');
      that._t = NAME;      // collection type
      that._i = id++;      // collection id
      that._l = undefined; // leak store for uncaught frozen objects
      if (iterable != undefined) forOf(iterable, IS_MAP, that[ADDER], that);
    });
    redefineAll(C.prototype, {
      // 23.3.3.2 WeakMap.prototype.delete(key)
      // 23.4.3.3 WeakSet.prototype.delete(value)
      'delete': function (key) {
        if (!isObject(key)) return false;
        var data = getWeak(key);
        if (data === true) return uncaughtFrozenStore(validate(this, NAME))['delete'](key);
        return data && $has(data, this._i) && delete data[this._i];
      },
      // 23.3.3.4 WeakMap.prototype.has(key)
      // 23.4.3.4 WeakSet.prototype.has(value)
      has: function has(key) {
        if (!isObject(key)) return false;
        var data = getWeak(key);
        if (data === true) return uncaughtFrozenStore(validate(this, NAME)).has(key);
        return data && $has(data, this._i);
      }
    });
    return C;
  },
  def: function (that, key, value) {
    var data = getWeak(anObject(key), true);
    if (data === true) uncaughtFrozenStore(that).set(key, value);
    else data[that._i] = value;
    return that;
  },
  ufstore: uncaughtFrozenStore
};


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
!function() {
"use strict";

// EXTERNAL MODULE: ./node_modules/decanter/core/src/js/components/alert/alert.js
var alert_alert = __webpack_require__(8766);
// EXTERNAL MODULE: ./node_modules/decanter/core/src/js/components/accordion/accordion.js
var accordion = __webpack_require__(4814);
// EXTERNAL MODULE: ./node_modules/decanter/core/src/js/core/core.js
var core = __webpack_require__(619);
;// ./node_modules/decanter/core/src/js/components/main-nav/globals.js
// ---------------------------------------------------------------------------
// Global variables and functions shared amongst the nav code
// ---------------------------------------------------------------------------

// Variables

/**
 *  Global record of all main navs on the page
 *  - populated in the document.ready function in main-nav.js
 *  - used by closeAllMobileNavs
 * @type {Array}
 */
var theNavs = [];

/**
 *  Global record of all sub navs on the page (may be in different main navs
 *  - populated by the NavItem constructor
 *  - used by closeAllSubNavs
 * @type {Array}
 */
var theSubNavs = [];

// Functions

/**
 * Close all subnavs on the page
 */
const closeAllSubNavs = () => {
  theSubNavs.forEach(
    subNav => { subNav.closeSubNav(); }
  );
};

/**
 * Close all mobile navs on the page
 */
const closeAllMobileNavs = () => {
  theNavs.forEach(
    theNav => { theNav.closeMobileNav(); }
  );
};

;// ./node_modules/decanter/core/src/js/utilities/keyboard.js
// ---------------------------------------------------------------------------
// Keyboard helper functions
// ---------------------------------------------------------------------------

const isHome = theKey => (theKey === 'Home' || theKey === 122);
const isEnd = theKey => (theKey === 'End' || theKey === 123);
const isTab = theKey => (theKey === 'Tab' || theKey === 9);
const isEsc = theKey => (theKey === 'Escape' || theKey === 'Esc' || theKey === 27);
const isSpace = theKey => (theKey === ' ' || theKey === 'Spacebar' || theKey === 32);
const isEnter = theKey => (theKey === 'Enter' || theKey === 13);
const isLeftArrow = theKey => (theKey === 'ArrowLeft' || theKey === 'Left' || theKey === 37);
const isRightArrow = theKey => (theKey === 'ArrowRight' || theKey === 'Right' || theKey === 39);
const isUpArrow = theKey => (theKey === 'ArrowUp' || theKey === 'Up' || theKey === 38);
const isDownArrow = theKey => (theKey === 'ArrowDown' || theKey === 'Down' || theKey === 40);

/**
 * Return a consistent string for each key validation.
 *
 * @param {*} theKey the code from a keypress event.
 *
 * @return {String} A string name for the key that was pressed.
 */
const normalizeKey = (theKey) => {

  // Key Value Map of the normalized string and the check function.
  const map = {
    home: isHome,
    end: isEnd,
    tab: isTab,
    escape: isEsc,
    space: isSpace,
    enter: isEnter,
    arrowLeft: isLeftArrow,
    arrowRight: isRightArrow,
    arrowUp: isUpArrow,
    arrowDown: isDownArrow
  };

  // Loop through the key/val object and run the check function (val) in order
  // to return the normalized string (key)
  for (var entry of Object.entries(map)) {
    if (entry[1](theKey)) {
      return entry[0];
    }
  }

  return false;
};

;// ./node_modules/decanter/core/src/js/utilities/events.js
/**
 * Create an event with the specified name in a browser-agnostic way.
 *
 * @param {string} eventName - the name of the event
 * @param {Object} data - Additional data along with the event.
 *
 * @return {Event} - instance of event which can be dispatched / listened for
 */
const createEvent = (eventName, data) => {
  if (typeof eventName !== 'string' || eventName.length <= 0) {
    return null;
  }
  // Modern browsers.
  if (typeof Event == 'function') {
    return new Event(eventName, data);
  }
  // IE
  else {
    let ev = document.createEvent('UIEvent');
    ev.initEvent(eventName, true, true, data);
    return ev;
  }
};

;// ./node_modules/decanter/core/src/js/components/main-nav/NavItem.js





/**
 * Represent an item in a navigation menu. May be a direct link or a subnav
 * trigger.
 *
 * @prop {HTMLLIElement}   item   - the <li> in the DOM that is the NavItem
 * @prop {HTMLElement|Nav} nav    - the Nav that contains the element.
 *                                  May be a main nav (<nav>) or subnav (Nav).
 * @prop {HTMLLIElement}   link   - the <a> in the DOM that is contained in
 *                                  item (the <li>).
 * @prop {Nav}             subNav - if item is the trigger for a subnav, this
 *                                  is an instonce Nav that models the subnav.
 */
class NavItem {

  /**
   * Create a NavItem
   * @param {HTMLLIElement}   item  - The <li> that is the NavItem in the DOM.
   * @param {HTMLElement|Nav} nav   - The Nav that contains the element. May
   *                                  be a main nav (<nav>) or a subnav (Nav).
   */
  constructor(item, nav) {
    this.item = item;
    this.nav = nav;
    this.link = this.item.querySelector('a');
    this.subNav = null;
    this.item.addEventListener('keydown', this);

    if (this.isSubNavTrigger()) {
      this.subNav = new Nav(this);
      // Add custom events to alert others when a subnav opens or closes.
      // this.openEvent is dispatched in this.openSubNav().
      this.openEvent = createEvent('openSubnav');
      // this.closeEvent is dispatched in this.closeSubNav().
      this.closeEvent = createEvent('closeSubnav');

      // Maintain global list of subnavs for closeAllSubNavs().
      theSubNavs.push(this);
      this.item.addEventListener('click', this);
    }
  }

  // -------------------------------------------------------------------------
  // Helper Methods.
  // -------------------------------------------------------------------------

  /**
   * Is this the first item in the containing Nav?
   *
   * @return {Boolean}
   *  Wether or not the item is the first item.
   */
  isFirstItem() {
    return this.nav.items.indexOf(this) === 0;
  }

  /**
   * Is this the last item in the containing Nav?
   *
   * @return {Boolean}
   *  Wether or not the item is the last item.
   */
  isLastItem() {
    return this.nav.items.indexOf(this) === (this.nav.items.length - 1);
  }

  /**
   * Is this a trigger that opens / closes a subnav?
   *
   * @return {Boolean}
   *  Wether or not the item is the sub nav trigger item.
   */
  isSubNavTrigger() {
    return this.item.lastElementChild.tagName.toUpperCase() === 'UL';
  }

  /**
   * Is this a component of a subnav - either the trigger or a nav item?
   *
   * @return {Boolean}
   *  Wether or not the item is a subnav item.
   */
  isSubNavItem() {
    return (this.isSubNavTrigger() || this.nav.isSubNav());
  }

  /**
   * Is this expanded? Can only return TRUE if this is a subnav trigger.
   *
   * @return {Boolean}
   *  Wether or not the item is expanded.
   */
  isExpanded() {
    return this.link.getAttribute('aria-expanded') === 'true';
  }

  /**
   * Set whether or not this is expanded.
   * Only meaningful if this is a subnav trigger.
   *
   * @param {String} value - What to set the aria-expanded attribute of this's
   *                         link to.
   */
  setExpanded(value) {
    this.link.setAttribute('aria-expanded', value);
  }

  // -------------------------------------------------------------------------
  // Functional Methods.
  // -------------------------------------------------------------------------

  /**
   * Handles the opening of a sub-nav.
   *
   * If this is a subnav trigger, open the corresponding subnav.
   * Optionally force focus on the first element in the subnav
   * (for keyboard nav).
   *
   * @param {Boolean} focusOnFirst - whether or not to also focus on the first
   *                                 element in the subnav
   */
  openSubNav(focusOnFirst = true) {
    closeAllSubNavs();

    if (this.isSubNavTrigger()) {
      this.item.classList.add('su-main-nav__item--expanded');
      this.setExpanded('true');
      if (focusOnFirst) {
        this.subNav.focusOn('first');
      }
      this.item.dispatchEvent(this.openEvent);
    }
  }

  /**
   * Handles the closing of a subnav.
   *
   * If this is a subnav trigger or an item in a subnav, close the
   * corresponding subnav. Optionally force focus on the trigger.
   *
   * @param {Boolean} focusOnTrigger - Whether or not to also focus on the
   *                                 subnav's trigger.
   */
  closeSubNav(focusOnTrigger = false) {
    if (this.isSubNavTrigger()) {
      if (this.isExpanded()) {
        this.item.classList.remove('su-main-nav__item--expanded');
        this.setExpanded('false');
        if (focusOnTrigger) {
          this.link.focus();
        }
        this.item.dispatchEvent(this.closeEvent);
      }
    }
    else if (this.isSubNavItem()) {
      // This.nav.elem should be a subNavTrigger.
      this.nav.elem.closeSubNav(focusOnTrigger);
    }
  }

  // -------------------------------------------------------------------------
  // Event Handlers.
  // -------------------------------------------------------------------------

  /**
   * Handler for all events attached to an instance of this class. This method
   * must exist when events are bound to an instance of a class
   * (vs a function). This method is called for all events bound to an
   * instance. It inspects the instance (this) for an appropriate handler
   * based on the event type. If found, it dispatches the event to the
   * appropriate handler.
   *
   * @param {KeyboardEvent} event - The keyboard event.
   *
   * @return {*}
   *   Whatever the dispatched handler returns (in our case nothing)
   */
  handleEvent(event) {
    event = event || window.event;

    // If this class has an onEvent method (onClick, onKeydown) invoke it.
    const handler = 'on'
      + event.type.charAt(0).toUpperCase()
      + event.type.slice(1);

    if (typeof this[handler] === 'function') {
      // The element that was clicked.
      const target = event.target || event.srcElement;
      return this[handler](event, target);
    }
  }

  /**
   * Handler for keydown events. keydown is bound to all NavItem's.
   * Dispatched from this.handleEvent().
   *
   * @param {KeyboardEvent} event - The keyboard event object.
   * @param {HTMLElement} target  - The HTML element target.
   */
  onKeydown(event, target) {
    const theKey = event.key || event.keyCode;

    // Handler for the space and enter key.
    if (isSpace(theKey) || isEnter(theKey)) {
      event.preventDefault();
      event.stopPropagation();
      if (this.isSubNavTrigger()) {
        this.openSubNav();
      }
      else {
        window.location = this.link;
      }
    }
    // Handler for the down arrow key.
    else if (isDownArrow(theKey)) {
      event.preventDefault();
      event.stopPropagation();
      if (this.nav.isDesktopNav()) {
        if (this.isSubNavTrigger()) {
          this.openSubNav();
        }
        else {
          this.nav.focusOn('next', this);
        }
      }
      else {
        this.nav.focusOn('next', this);
      }
    }
    // Handler for the up arrow key.
    else if (isUpArrow(theKey)) {
      event.preventDefault();
      event.stopPropagation();
      this.nav.focusOn('prev', this);
    }
    // Handler for the left arrow key.
    else if (isLeftArrow(theKey)) {
      event.preventDefault();
      event.stopPropagation();
      if (this.nav.isDesktopNav()) {
        if (this.nav.isSubNav()) {
          this.closeSubNav();
          let parent = this.nav.getParentNav();
          // Focus on the previous item in the parent nav.
          parent.focusOn('prev', this.nav.elem);
        }
        else {
          this.nav.focusOn('prev', this);
        }
      }
      else {
        if (this.isSubNavItem()) {
          // Close the subnav and put the focus on the trigger.
          this.closeSubNav(true);
        }
      }
    }
    // Handler for the right arrow key.
    else if (isRightArrow(theKey)) {
      event.preventDefault();
      event.stopPropagation();
      if (this.nav.isDesktopNav()) {
        if (this.nav.isSubNav()) {
          this.closeSubNav();
          let parent = this.nav.getParentNav();
          // Focus on the next item in the parent nav.
          parent.focusOn('next', this.nav.elem);
        }
        else {
          this.nav.focusOn('next', this);
        }
      }
      else {
        if (this.isSubNavTrigger()) {
          this.openSubNav();
        }
      }
    }
    // Handler for the home key.
    else if (isHome(theKey)) {
      this.nav.focusOn('first');
    }
    // Handler for the end key.
    else if (isEnd(theKey)) {
      this.nav.focusOn('last');
    }
    // Handler for the tab key.
    else if (isTab(theKey)) {
      event.stopPropagation();
      const shifted = event.shiftKey;
      if (this.isSubNavItem()
        && ((!shifted && this.isLastItem())
          || (shifted && this.isFirstItem()))
      ) {
        this.closeSubNav(true);
      }
    }
  }

  /**
   * Handler for click events.
   *
   * Dispatched from this.handleEvent().
   * Click is only bound to subnav triggers. However, click bubbles up from
   * subnav items to the trigger.
   *
   * @param {KeyboardEvent} event - The keyboard event object.
   * @param {HTMLElement} target  - The HTML element target.
   */
  onClick(event, target) {
    if (this.isExpanded()) {
      this.closeSubNav();
    }
    else {
      this.openSubNav(false);
    }
    // If the click is directly on the trigger, then we're done.
    if (target === this.link) {
      event.preventDefault();
      event.stopPropagation();
    }
  }

}

;// ./node_modules/decanter/core/src/js/components/main-nav/Nav.js





/**
 * Represent a navigation menu. May be the top nav or a subnav.
 *
 * @prop {HTMLElement|NavItem} elem       - The element that is the nav. May
 *                                          be a main nav (<nav>) or a subnav
 *                                          (NavItem).
 * @prop {Nav}                 topNav     - The instance of Nav that models
 *                                          the top nav. If this is the top
 *                                          nav, topNav === this.
 * @prop {HTMLButtonElement}   toggle     - The <button> in the DOM that
 *                                          toggles the menu on mobile. NULL
 *                                          if this is a subnav.
 * @prop {String}              toggleText - The initial text of the mobile
 *                                          toggle (so we can reset it when
 *                                          the mobile nav is closed).
 * @prop {Array}               items      - Instances of NavItem that model
 *                                          each element in the nav
 */
class Nav {

  /**
   * Create a Nav
   *
   * @param {HTMLElement|NavItem} elem - The element that is the nav menu.
   *                                     May be a main nav (<nav>) or a subnav
   *                                     (NavItem).
   */
  constructor(elem) {
    this.elem = elem;
    this.topNav = this.getTopNav();
    // If this is a subnav, we need the correpsonding HTMLElement for
    // .querySelector()
    if (elem instanceof NavItem) {
      elem = elem.item;
    }
    this.toggle = elem.querySelector(elem.tagName + ' > button');
    this.toggleText = this.toggle ? this.toggle.innerText : '';
    this.items = [];
    // Add custom events to alert others when the mobile nav
    // opens or closes.
    // this.openEvent is dispatched in this.openMobileNav().
    this.openEvent = createEvent('openNav');
    // this.closeEvent is dispatched in this.closeMobileNav().
    this.closeEvent = createEvent('closeNav');

    // Initialize items
    let items = elem.querySelectorAll(elem.tagName + ' > ul > li');
    items.forEach(
      item => {
        this.items.push(new NavItem(item, this));
      }
    );

    elem.addEventListener('keydown', this);

    if (this.toggle) {
      this.toggle.addEventListener('click', this);
    }
  }

  // -------------------------------------------------------------------------
  // Helper Methods.
  // -------------------------------------------------------------------------

  /**
   * Get the instance of Nav that represents the top level nav of this
   * instance.
   *
   * @return {Nav}
   *  Returns the navigation instance.
   */
  getTopNav() {
    let nav = this;
    while (nav.elem instanceof NavItem) {
      // If nav is the main nav, nav.elem will be an HTMLElement
      // (the <nav> element).
      // If nav.elem is a NavItem, then this is a subNav, so get the Nav that
      // contains the NavItem.
      nav = nav.elem.nav;
    }
    return nav;
  }

  /**
   * Get the instance of Nav that represents the parent of this instance.
   * If this is the top nav, return this so you can safely call methods on it.
   *
   * @return {Nav}
   *   Returns the navigation instance.
   */
  getParentNav() {
    return this.isSubNav() ? this.elem.nav : this;
  }


  /**
   * Is this expanded?
   * If this is a subnav, ask the subnav (NavItem) if it's expanded.
   * Otherwise (this is the top nav), check aria-expanded.
   *
   * @return {Boolean}
   *   Returns wether or not the item is expanded.
   */
  isExpanded() {
    if (this.elem instanceof NavItem) {
      return this.elem.isExpanded();
    }

    return this.elem.getAttribute('aria-expanded') === 'true';
  }

  /**
   * Set whether or not this is expanded.
   * If this is a subnav, let the subnav (NavItem) handled it.
   * Otherwise (this is the top nav), set aria-expanded.
   *
   * @param {String} value - What to set the aria-expanded attribute of
   *                         this's link to.
   */
  setExpanded(value) {
    if (this.elem instanceof NavItem) {
      this.elem.setExpanded(value);
    }
    else {
      this.elem.setAttribute('aria-expanded', value);
      if (this.toggle) {
        this.toggle.setAttribute('aria-expanded', value);
      }
    }
  }

  /**
   * Is this rendering the desktop style for the nav?
   *
   * @return {Boolean}
   *  Returns wether or not it is desktop navigation.
   */
  isDesktopNav() {
    return getComputedStyle(this.topNav.toggle).display === 'none';
  }

  /**
   * Is this the top nav?
   *
   * @return {Boolean}
   *  Returns wether or not it is the top nav item.
   */
  isTopNav() {
    return this.topNav === this;
  }

  /**
   * Is this a subnav?
   *
   * @return {Boolean}
   *  Returns wether or not this is a subnav item.
   */
  isSubNav() {
    return this.topNav !== this;
  }

  /**
   * Get the first item in this nav.
   *
   * @return {NavItem}
   *  Returns wether or not this is the first item.
   */
  getFirstItem() {
    return this.items.length ? this.items[0] : null;
  }

  /**
   * Get the last item in this nav.
   *
   * @return {NavItem}
   *  Returns wether or not this is the last item.
   */
  getLastItem() {
    return this.items.length ? this.items[this.items.length - 1] : null;
  }

  /**
   * Get the link associated with the first item in this nav.
   *
   * @return {HTMLAnchorElement}
   *  Returns the very first link.
   */
  getFirstLink() {
    return this.items.length ? this.getFirstItem().link : null;
  }

  /**
   * Get the link associated with the last item in this nav.
   *
   * @return {HTMLAnchorElement}
   *  Returns the very last link.
   */
  getLastLink() {
    return this.items.length ? this.getLastItem().link : null;
  }

  // -------------------------------------------------------------------------
  // Functional methods
  // -------------------------------------------------------------------------

  /**
   * Set the focus on the specified link in this nav.
   *
   * @param {String|Number} link - 'first' | 'last' | 'next'
   *                                | 'prev' | numerical index
   * @param {NavItem} currentItem - If link is 'next' or 'prev', currentItem
   *                                is the NavItem that next / prev is
   *                                relative to.
   */
  focusOn(link, currentItem = null) {
    let currentIndex = null;
    let lastIndex = null;
    if (currentItem) {
      currentIndex = this.items.indexOf(currentItem);
      lastIndex = this.items.length - 1;
    }
    switch (link) {
      case 'first':
        this.getFirstLink().focus();
        break;

      case 'last':
        this.getLastLink().focus();
        break;

      case 'next':
        if (currentIndex === lastIndex) {
          this.getFirstLink().focus();
        }
        else {
          this.items[currentIndex + 1].link.focus();
        }
        break;

      case 'prev':
        if (currentIndex === 0) {
          this.getLastLink().focus();
        }
        else {
          this.items[currentIndex - 1].link.focus();
        }
        break;

      default:
        if (Number.isInteger(link) && link >= 0 && link < this.items.length) {
          this.items[link].link.focus();
        }
        break;
    }
  }

  /**
   * Close any mobile navs that might be open, then mark this mobile nav open.
   * Optionally force focus on the first element in the nav (for keyboard nav)
   *
   * @param {Boolean} focusOnFirst - Whether or not to also focus on the
   *                                 first element in the subnav.
   */
  openMobileNav(focusOnFirst = true) {
    closeAllMobileNavs();

    this.setExpanded('true');
    this.toggle.innerText = 'Close';
    if (focusOnFirst) {
      // Focus on the first top level link.
      this.focusOn('first');
    }
    // Alert others the mobile nav has opened.
    this.elem.dispatchEvent(this.openEvent);
  }

  /**
   * Mark this mobile closed, and restore the button text to what it was
   * initially.
   */
  closeMobileNav() {
    if (this.isExpanded()) {
      this.setExpanded('false');
      this.toggle.innerText = this.toggleText;
      // Alert others the mobile nav has closed.
      this.elem.dispatchEvent(this.closeEvent);
    }
  }

  // -------------------------------------------------------------------------
  // Event handlers
  // -------------------------------------------------------------------------

  /**
   * Handler for all events attached to an instance of this class. This method
   * must exist when events are bound to an instance of a class
   * (vs a function). This method is called for all events bound to an
   * instance. It inspects the instance (this) for an appropriate handler
   * based on the event type. If found, it dispatches the event to the
   * appropriate handler.
   *
   * @param {KeyboardEvent} event - The keyboard event object.
   *
   * @return {*}
   *  Whatever the dispatched handler returns (in our case nothing)
   */
  handleEvent(event) {
    event = event || window.event;
    // If this class has an onEvent method, e.g. onClick, onKeydown,
    // invoke it.
    const handler = 'on'
      + event.type.charAt(0).toUpperCase()
      + event.type.slice(1);

    if (typeof this[handler] === 'function') {
      // The element that was clicked.
      const target = event.target || event.srcElement;
      return this[handler](event, target);
    }
  }

  /**
   * Handler for click events. click is only bound to the mobile toggle.
   * Dispatched from this.handleEvent().
   *
   * @param {KeyboardEvent} event   - The keyboard event object.
   * @param {HTMLElement}   target  - The HTML Element target object.
   */
  onClick(event, target) {
    if (target === this.toggle) {
      event.preventDefault();
      event.stopPropagation();
      if (this.isExpanded()) {
        this.closeMobileNav();
      }
      else {
        this.openMobileNav(false);
      }
    }
  }

  /**
   * Handler for keydown events. keydown is bound to all Nav's.
   * Dispatched from this.handleEvent().
   *
   * @param {KeyboardEvent} event   - The keyboard event object.
   * @param {HTMLElement}   target  - The HTML Element target object.
   */
  onKeydown(event, target) {
    const theKey = event.key || event.keyCode;

    if (isEsc(theKey)) {
      if (this.isTopNav()) {
        if (!this.isDesktopNav()) {
          event.preventDefault();
          event.stopPropagation();
          this.closeMobileNav();
          this.toggle.focus();
        }
      }
      else {
        if (this.isExpanded()) {
          event.preventDefault();
          event.stopPropagation();
          this.elem.closeSubNav(true);
        }
      }
    }
    else if (isEnter(theKey) || isSpace(theKey)) {
      if (target === this.toggle) {
        event.preventDefault();
        event.stopPropagation();
        if (!this.isExpanded()) {
          this.openMobileNav();
        }
      }
    }
  }

}

;// ./node_modules/decanter/core/src/js/components/main-nav/main-nav.js




document.addEventListener('DOMContentLoaded', event => {

  // The css class that this following behaviour is applied to.
  const navClass = 'su-main-nav';
  // All main navs.
  const navs = document.querySelectorAll('.' + navClass);

  // Process each nav.
  let firstZindex;
  navs.forEach((nav, index) => {
    // Remove the class that formats the nav for browsers with javascript disabled.
    nav.classList.remove('no-js');

    // Create an instance of Nav, which in turn creates appropriate instances of NavItem.
    const theNav = new Nav(nav);

    // Remember the nav for closeAllMobileNavs().
    theNavs.push(theNav);

    // Manage zindexes in case there are multiple navs near enough for subnavs to overlap.
    // Rare, but it happens in the styleguide.
    if (index === 0) {
      firstZindex = getComputedStyle(nav, null).zIndex;
    }
    else {
      nav.style.zIndex = firstZindex - 300 * index;
    }
  }); // navs.forEach

  // Clicking anywhere outside a nav closes all navs.
  document.addEventListener('click', event => {
    // The element that was clicked.
    const target = event.target || event.srcElement;
    // If target is not under a main nav close all navs.
    if (!target.matches('.' + navClass + ' ' + target.tagName)) {
      closeAllSubNavs();
      closeAllMobileNavs();
    }
  }, false);

}); // on DOMContentLoaded.

;// ./node_modules/decanter/core/src/js/components/secondary-nav/common/globals.js
// The css class that this following behaviour is applied to.
const secondaryNavClass = 'su-secondary-nav';

// All Secondary navs.
var secondaryNavs = document.querySelectorAll('.' + secondaryNavClass);

;// ./node_modules/decanter/core/src/js/components/nav/ActivePath.js
/**
 * ActivePath
 *
 * This class contains features and functionality for handling the finding and
 * setting of the active trail of a menu.
 */
class ActivePath {

  /**
   * Initialize.
   *
   * @param {HTMLElement} element The DOM object of the navigation menu.
   * @param {Mixed} item          The Navigation Class.
   * @param {Object} options      An optional object of meta information.
   */
  constructor(element, item, options = {}) {
    this.elem = element;
    this.item = item;
    // CSS Class properties.
    this.itemActiveClass = options.itemActiveClass || 'active';
    this.itemActiveTrailClass = options.itemActiveTrailClass || 'active-trail';
    this.itemExpandedClass = options.itemExpandedClass || 'expanded';
  }

  /**
   * Dynamically add an active path to the menu tree.
   *
   * Find all links with the current window's url and add the
   * options.itemActiveClass class to the LI element container all the way up
   * the menu tree back to the root.
   */
  setActivePath() {
    let path = window.location.pathname;
    let anchor = window.location.hash || '';
    let query = window.location.search || '';
    let currentItem = false;

    // Queries to run to find matching active paths in order of unqiueness.
    let finders = [
      this.elem.querySelector("a[href*='" + anchor + "']"),
      this.elem.querySelector("a[href*='" + query + "']"),
      this.elem.querySelector("a[href='" + path + query + anchor + "']"),
      this.elem.querySelector("a[href*='" + path + query + "']")
    ];

    // Go through the queries and see if we have any results.
    finders.forEach(function (val) {
      if (!currentItem && val) {
        currentItem = val;
      }
    });

    // Can't find anything. End.
    if (!currentItem) {
      return;
    }

    // While we have parents go up and add the active class.
    while (currentItem) {

      // If we are on a LI element we need to add the active class.
      if (currentItem.tagName === 'LI') {
        currentItem.classList.add(this.itemActiveClass);
        break;
      }

      // Always increment.
      currentItem = currentItem.parentNode;
    }
  }

  /**
   * Expand all menus in the active path.
   *
   * After this.setActivePath() has been run or the itemActiveClass has been set
   * on all the appropriate menu items go through the nav and expand the
   * subNavItems that contain activeClass items.
   */
  expandActivePath() {
    let actives = this.elem.querySelectorAll('.' + this.itemActiveClass);
    if (actives.length) {
      actives.forEach(
        element => {

          // While we have parents go up and add the active class.
          while (element) {
            // End when we get to the parent nav item stop.
            if (element === this.elem) {
              // Stop at the top most level.
              break;
            }

            // If we are on a LI element we need to add the active class.
            if (element.tagName === 'LI') {
              element.classList.add(this.itemExpandedClass);
              element.classList.add(this.itemActiveTrailClass);
              // "Hook" of sorts.
              if (typeof this.item.expandActivePathItem == 'function') {
                this.item.expandActivePathItem(element);
              }
            }

            // Always increment.
            element = element.parentNode;
          }
        }
      );
    }
  }
}

;// ./node_modules/decanter/core/src/js/components/nav/EventHandlerDispatch.js


/**
 * EventHandlerDispatch Class
 *
 * This class provides dynamic handling of click and keyboard events and can be
 * attached to any class/HTMLElement.
 */
class EventHandlerDispatch {

  /**
   * Initialize.
   *
   * @param {HTMLElement} element   The HTMLElement to bind listeners to.
   * @param {type}      handler   The Javascript Class instance with the
   *                                eventRegistry property.
   */
  constructor(element, handler) {
    this.elem = element;
    this.handler = handler;
    this.createEventListeners();
  }

  /**
   * Create new event listeners.
   */
  createEventListeners() {
    // What to do when a key is down?
    this.elem.addEventListener('keydown', this);

    // Listen to the click so we can act on it.
    this.elem.addEventListener('click', this);

    // Listen to custom events so we can act on it.
    this.elem.addEventListener('preOpenSubnav', this);

    // Listen to custom events so we can act on it.
    this.elem.addEventListener('postOpenSubnav', this);
  }

  /**
   * Handler for all events attached to an instance of this class.
   *
   * This method must exist when events are bound to an instance of a class
   * (vs a function). This method is called for all events bound to an
   * instance. It inspects the instance (this) for an appropriate handler
   * based on the event type. If found, it dispatches the event to the
   * appropriate handler.
   *
   * @param {Event} event - The triggering event.
   */
  handleEvent(event) {
    event = event || window.event;

    // Create an event signature.
    const eventMethod = 'on'
      + event.type.charAt(0).toUpperCase()
      + event.type.slice(1);

    // What was clicked.
    const target = event.target || event.srcElement;

    if (eventMethod === 'onKeydown') {
      this.onKeydown(event, target);
    }
    else if (eventMethod === 'onClick') {
      this.onClick(event, target);
    }
    else {
      this.callEvent(eventMethod, event, target);
    }
  }

  /**
   * Handler for keydown events.
   *
   * @param {KeyboardEvent} event - The keyboard event object.
   * @param {HTMLElement} target  - The HTML element target.
   */
  onKeydown(event, target) {
    let theKey = event.key || event.keyCode;
    let normalized = normalizeKey(theKey);

    // We don't know or need to handle the key that was pressed.
    if (!normalized) {
      return;
    }

    // Prepare a dynamic handler.
    let eventMethod = 'onKeydown'
      + normalized.charAt(0).toUpperCase()
      + normalized.slice(1);

    // Do eet.
    this.callEvent(eventMethod, event, target);
  }

  /**
   * Handler for click events.
   *
   * @param  {Event} event  A Javascript event.
   * @param  {HTMLElement} target The target of the event.
   */
  onClick(event, target) {
    this.callEvent('onClick', event, target);
  }

  /**
   * The event handler
   *
   * Initializes and executes an object to handle the Javascript Event as
   * defined by the handlers eventRegistry.
   *
   * @param  {String} eventMethod A string key for the eventRegistry;
   * @param  {Event} event        The Javascript event.
   * @param  {HTMLElement} target The DOM object that the event is triggered on.
   */
  callEvent(eventMethod, event, target) {
    if (typeof this.handler.eventRegistry[eventMethod] === 'function') {
      var eventObj = new this.handler.eventRegistry[eventMethod](this.handler, event, target);
      eventObj.init();
    }
  }

}

;// ./node_modules/decanter/core/src/js/components/nav/ElementFetcher.js
/**
 * ElementFetcher Class
 *
 * Provides a relative named DOM navigator for quickly getting elements relative
 * to the provided context.
 */
class ElementFetcher {

  /**
   * Initialize.
   *
   * @param {HTMLElement} element   The DOM object to use.
   * @param {String} what           A named string.
   */
  constructor(element, what) {
    this.item = element;
    this.what = what;
  }

  /**
   * Attempt to retrieve an item.
   *
   * @return {Boolean|HTMLElement} An element or false if `what` is not found.
   */
  fetch() {
    try {
      switch (this.what) {
        case 'first':
          return this.item.parentNode.firstElementChild.firstChild;
        case 'last':
          return this.item.parentNode.lastElementChild.firstChild;
        case 'firstElement':
          return this.item.parentNode.firstElementChild;
        case 'lastElement':
          return this.item.parentNode.lastElementChild;
        case 'next':
          return this.item.nextElementSibling.querySelector('a');
        case 'prev':
          return this.item.previousElementSibling.querySelector('a');
        case 'nextElement':
          return this.item.nextElementSibling;
        case 'prevElement':
          return this.item.previousElementSibling;
        case 'parentItem':
          var node = this.item.parentNode.parentNode;
          if (node.tagName === 'NAV') { return false; }
          return node.querySelector('a');
        case 'parentButton':
          return this.item.parentNode.parentNode.querySelector('button');
        case 'parentNav':
          return this.item.parentNode.parentNode;
        case 'parentNavLast':
          return this.item.parentNode.parentNode.parentNode.lastElementChild.querySelector('a');
        case 'parentNavFirst':
          return this.item.parentNode.parentNode.parentNode.firstElementChild.querySelector('a');
        case 'parentNavNext':
          return this.item.parentNode.parentNode.nextElementSibling;
        case 'parentNavNextItem':
          return this.item.parentNode.parentNode.nextElementSibling.querySelector('a');
        case 'parentNavPrev':
          return this.item.parentNode.parentNode.previousElementSibling;
        case 'parentNavPrevItem':
          return this.item.parentNode.parentNode.previousElementSibling.querySelector('a');
        case 'firstSubnavLink':
          return this.item.querySelector(':scope > ul li a');
        case 'firstSubnavItem':
          return this.item.querySelector(':scope > ul li');
        case 'subnav':
          return this.item.querySelector(':scope > ul');
        default:
          return false;
      }
    }
    catch (err) {
      return false;
    }
  }
}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/common/events/EventAbstract.js


/**
 * EventAbstract
 *
 * An abstract class for creating an interface for working with the
 * EventHandlerDispatch class. This is the signature for all instances
 * that are evoked through the eventRegistry.
 */
class EventAbstract {

  /**
   * Initialize.
   *
   * @param {Object|Mixed} item The javascript object instance that this is bound to.
   * @param {KeyboardEvent|MouseEvent} event - The event object.
   * @param {HTMLElement} target  - The HTML element target.
   */
  constructor(item, event, target) {
    this.item = item;
    this.elem = item.elem;
    this.masterNav = item.masterNav;
    this.parentNav = item.parentNav;
    this.target = target;
    this.event = event;
  }

  /**
   * A validation shorcut that should pass before running exec().
   *
   * @return {Boolean} Wether or not the event target is what this instance is bound to.
   */
  isOnTarget() {
    // Check to see if the event target is what this instance is bound to.
    if (this.target === this.elem) {
      return true;
    }
    return false;
  }

  /**
   * A validation method that should pass before running exec().
   *
   * @return {Boolean} Wether or not validation passes.
   */
  validate() {
    // Only act on me.
    if (!this.isOnTarget()) {
      return false;
    }
    return true;
  }

  /**
   * Interface method.
   *
   * When evoking this abstract instance you should use this method as your
   * iterface for calling the action.
   */
  init() {
    if (this.validate()) {
      this.exec();
    }
  }

  /**
   * Shortcut function to find a DOM element.
   *
   * This is a helper function that uses a ElementFetcher instance to navigate
   * and traverse the DOM relative to the current context.
   *
   * @param  {String} what A keyword for what we are trying to find.
   * @param  {HTMLElement} context The relative starting location for the finder.
   * @return {Boolean|HTMLElement} False if not found or an HTMLElement.
   */
  getElement(what, context = this.elem.parentNode) {
    var fetcher = new ElementFetcher(context, what);
    return fetcher.fetch();
  }

}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/common/events/OnEsc.js


/**
 * OnEsc
 *
 * Event action handler class.
 */
class OnEsc extends EventAbstract {

  /**
   * Execute the action to the event.
   */
  exec() {
    this.event.preventDefault();
    let node = false;

    if (this.item.getDepth() > 1) {
      this.event.stopPropagation();
      this.parentNav.closeSubNav();
      node = this.getElement('parentItem');
    }
    else {
      this.masterNav.closeAllSubNavs();
      node = this.getElement('first', this.item.parentNode);
    }

    if (node) {
      node.focus();
    }
  }

}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/common/events/OnSpace.js


/**
 * OnSpace
 *
 * Event action handler class.
 */
class OnSpace extends EventAbstract {

  /**
   * Execute the action to the event.
   */
  exec() {
    this.event.stopPropagation();
    this.event.preventDefault();
    window.location = this.target.getAttribute('href');
  }

}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/common/SecondaryNavAbstract.js





/**
 * SecondaryNav Class
 *
 * The most abstract version of a SecondaryNav. All Nav types should extend
 * this class in order to have a psuedo interface and default methods.
 */
class SecondaryNavAbstract {

  /**
   * Nav Abstract Constructor class.
   *
   * @param {HTMLElement} element    The html element to use as the parent for the nav list.
   * @param {Object} options      An object with key value pairs of configuration options.
   */
  constructor(element, options = {}) {
    // What HTML element this is bound to.
    this.elem = element;

    // Set some default options.
    var defaultOptions = {
      itemClass: 'su-secondary-nav__item',
      itemExpandedClass: 'su-secondary-nav__item--expanded',
      itemActiveClass: 'su-secondary-nav__item--current',
      itemActiveTrailClass: 'su-secondary-nav__item--active-trail',
      itemParentClass: 'su-secondary-nav__item--parent',
      eventRegistry: {}
    };

    // Merge with passed in options.
    this.options = Object.assign(defaultOptions, options);

    // Remove the no-js class.
    this.elem.classList.remove('no-js');

    // Assign the event dispatcher and event registry.
    this.eventRegistry = this.createEventRegistry(options);
    this.dispatch = new EventHandlerDispatch(element, this);

    // Handle the active state.
    this.activePath = new ActivePath(element, this, this.options);
    this.activePath.setActivePath();

    // Helper Item Variables.
    this.navItems = [];
    this.subNavItems = [];
    this.parentItemSelector = ':scope > ul > .' + this.options.itemParentClass;
    this.navItemSelector = ':scope > ul > .' + this.options.itemClass + ':not(.' + this.options.itemParentClass + ')';
  }

  /**
   * Add the additional state handling after the abstract option has run.
   *
   * @param  {HTMLElement} item The HTMLElement being acted upon.
   */
  expandActivePathItem(item) {
    // For any additional items outside of the core functions.
  }

  /**
   * Creates an event registry for handling types of events.
   *
   * This registry is used by the EventHandlerDispatch class to bind and
   * execute the events in the created property key.
   *
   * @param  {Object} options Options to merge in with the defaults.
   *
   * @return {Object} A key/value registry of events and handlers.
   */
  createEventRegistry(options) {

    var registryDefaults = {
      onKeydownEscape: OnEsc,
      onKeydownSpace: OnSpace
    };

    return Object.assign(registryDefaults, options.eventRegistry);
  }

  /**
   * Kickoff method for generating single and multi-tier nav instances.
   */
  createSubNavItems() {

    // Find all the single and multi-tier items.
    var parentItems = this.elem.querySelectorAll(this.parentItemSelector);
    var leafItems = this.elem.querySelectorAll(this.navItemSelector);

    // Sub Nav Items.
    if (parentItems.length >= 1) {
      this.createParentItems(parentItems);
    }

    // Regular Ol Items.
    if (leafItems.length >= 1) {
      this.createNavItems(leafItems);
    }
  }

  /**
   * Recursive loop for creating nested navigation instances.
   *
   * @param  {NodeList} items A set of sibling parent menu items.
   * @param  {Number} depth The current depth of recursion.
   * @param  {Object|Mixed} parentMenu The instance of the parent menu.
   */
  createParentItems(items, depth = 1, parentMenu = null) {
    items.forEach(
      item => {
        var itemLink = item.querySelector('a');
        var parentItems = item.querySelectorAll(this.parentItemSelector);
        var leafItems = item.querySelectorAll(this.navItemSelector);
        var nextDepth = depth + 1;
        var parentNav = null;

        // If we have a link add to it.
        if (itemLink) {
          parentNav = this.newParentItem(itemLink, depth, parentMenu);
        }

        // Nested Sub Nav Items.
        if (parentItems.length >= 1) {
          this.createParentItems(parentItems, nextDepth, parentNav);
        }

        // Nested Nav Items.
        if (leafItems.length >= 1) {
          this.createNavItems(leafItems, nextDepth, parentNav);
        }
      }
    );
  }

  /**
   * Recursive loop for creating single level navigation instances.
   *
   * @param  {NodeList} items A set of sibling parent menu items.
   * @param  {Number} depth The current depth of recursion.
   * @param  {Object|Mixed} parentMenu The instance of the parent menu.
   */
  createNavItems(items, depth = 1, parentMenu = null) {
    items.forEach(
      item => {
        var itemLink = item.querySelector('a');
        if (itemLink) {
          this.newNavItem(itemLink, depth, parentMenu);
        }
      }
    );
  }

  /**
   * Close all subNavItems in this Nav.
   */
  closeAllSubNavs() {
    this.subNavItems.forEach(
      (item, event) => {
        item.closeSubNav();
      }
    );
  }

  /**
   * Close only this subnav.
   */
  closeSubNav() {
    this.closeAllSubNavs();
  }
}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/common/events/OnHome.js


/**
 * OnHome
 *
 * Event action handler class.
 */
class OnHome extends EventAbstract {

  /**
   * Execute the action to the event.
   */
  exec() {
    this.event.preventDefault();
    var node = this.getElement('first');
    if (node) {
      node.focus();
    }
  }

}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/common/events/OnArrowDown.js



/**
 * OnArrowDown
 *
 * Event action handler class.
 */
class OnArrowDown extends EventAbstract {

  /**
   * Execute the action to the event.
   */
  exec() {
    this.event.preventDefault();

    // Go to the next item.
    let node = this.getElement('next');
    if (node) {
      node.focus();
      return;
    }

    // If a node is not found go to home.
    var eventHome = new OnHome(this.item, this.event, this.target);
    eventHome.init();
  }

}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/common/events/OnEnd.js


/**
 * OnEnd
 *
 * Event action handler class.
 */
class OnEnd extends EventAbstract {

  /**
   * Execute the action to the event.
   */
  exec() {
    this.event.preventDefault();
    var node = this.getElement('last');
    if (node) {
      node.focus();
    }
  }

}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/common/events/OnArrowUp.js



/**
 * OnArrowUp
 *
 * Event action handler class.
 */
class OnArrowUp extends EventAbstract {

  /**
   * Execute the action to the event.
   */
  exec() {
    this.event.preventDefault();

    // Go to the previous item.
    let node = this.getElement('prev');
    if (node) {
      node.focus();
      return;
    }

    // Default to the end..
    var eventEnd = new OnEnd(this.item, this.event, this.target);
    eventEnd.init();

  }
}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/common/events/OnArrowLeft.js



/**
 * OnArrowLeft
 *
 * Event action handler class.
 */
class OnArrowLeft_OnArrowLeft extends EventAbstract {

  /**
   * Execute the action to the event.
   */
  exec() {
    this.event.preventDefault();

    // If this is a nested item. Go back up a level.
    if (this.item.getDepth() > 1) {
      this.nestedLeft();
    }
    // Otherwise just to to the previous sibling.
    else if (this.item.getDepth() === 1) {
      this.firstLevelLeft();
    }
  }

  /**
   * Action to take on a first level left key press.
   */
  firstLevelLeft() {
    var upevent = new OnArrowUp(this.item, this.event, this.target);
    upevent.init();
  }

  /**
   * Action to take on a nested level left key press
   */
  nestedLeft() {
    let node = this.getElement('parentItem') || this.getElement('parentNavLast');
    this.parentNav.closeSubNav();

    if (node) {
      node.focus();
    }
  }

}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/common/events/OnArrowRight.js



/**
 * OnArrowRight
 *
 * Event action handler class.
 */
class OnArrowRight extends EventAbstract {

  /**
   * Execute the action to the event.
   */
  exec() {
    // If we are in the second level or more we check about traversing
    // the parent.
    if (this.item.getDepth() > 1) {
      let node = this.getElement('parentNavNext');
      this.parentNav.closeSubNav();

      if (node) {
        node.querySelector('a').focus();
      }
      // Go back to start.
      else {
        this.getElement('parentNavFirst').focus();
      }
    }
    else {
      var eventDown = new OnArrowDown(this.item, this.event, this.target);
      eventDown.init();
    }
  }

}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/common/events/OnEnter.js


/**
 * OnEnter
 *
 * Event action handler class.
 */
class OnEnter extends EventAbstract {

  /**
   * Execute the action to the event.
   */
  exec() {
    this.event.stopPropagation();
    this.event.preventDefault();
    window.location = this.target.getAttribute('href');
  }

}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/common/events/OnTab.js


/**
 * OnTab
 *
 * Event action handler class.
 */
class OnTab extends EventAbstract {

  /**
   * Execute the action to the event.
   */
  exec() {
    const shifted = event.shiftKey;
    let node = null;
    let firstItem = this.masterNav.elem.querySelector('a');
    let lastItem = this.masterNav.elem.firstElementChild.lastElementChild.querySelector('li:last-child');

    // If shift key is held.
    if (shifted) {
      node = this.getElement('prev');
      if (this.target === firstItem) {
        this.masterNav.closeAllSubNavs();
        return;
      }
    }
    // No shift key, just regular ol tab.
    else {
      node = this.getElement('next');
      if (this.target.parentNode === lastItem) {
        this.masterNav.closeAllSubNavs();
        return;
      }
    }

    // No nodes were found. Close up behind us.
    if (!node) {
      if (this.item.getDepth() > 1) {
        this.parentNav.closeSubNav();
      }
    }
  }
}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/common/SecondaryNavItem.js


// Keyboard control events.











/**
 * SecondaryNav Class
 */
class SecondaryNavItem {

  /**
   * Initialize.
   *
   * @param {HTMLElement} element      The HTMLElement to bind to.
   * @param {Object|Mixed} masterNav   The top most navigation instance.
   * @param {Object|Mixed} parentNav   The parent nav instance if available.
   * @param {Object} options           An object of metadata.
   */
  constructor(element, masterNav, parentNav = null, options = {}) {
    this.elem = element;
    this.item = element.parentNode;
    this.masterNav = masterNav;
    this.parentNav = parentNav;
    this.depth = options.depth || 1;

    // Assign the event dispatcher and event registry.
    this.eventRegistry = this.createEventRegistry(options);
    this.dispatch = new EventHandlerDispatch(element, this);
  }

  /**
   * Creates an event registry for handling types of events.
   *
   * This registry is used by the EventHandlerDispatch class to bind and
   * execute the events in the created property key.
   *
   * @param  {Object} options Options to merge in with the defaults.
   *
   * @return {Object} A key/value registry of events and handlers.
   */
  createEventRegistry(options) {

    var registryDefaults = {
      onKeydownHome: OnHome,
      onKeydownEnd: OnEnd,
      onKeydownTab: OnTab,
      onKeydownSpace: OnSpace,
      onKeydownEnter: OnEnter,
      onKeydownEscape: OnEsc,
      onKeydownArrowUp: OnArrowUp,
      onKeydownArrowRight: OnArrowRight,
      onKeydownArrowDown: OnArrowDown,
      onKeydownArrowLeft: OnArrowLeft_OnArrowLeft
    };

    return Object.assign(registryDefaults, options.eventRegistry);
  }

  /**
   * Get the level of nesting for this nav.
   *
   * @return {Integer} The integer of depth starting at 1.
   */
  getDepth() {
    return this.depth;
  }
}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/accordion/events/OnClick.js


/**
 * OnClick
 *
 * Event action handler class.
 */
class OnClick extends EventAbstract {

  /**
   * Execute the action to the event.
   */
  exec() {
    this.event.stopPropagation();
    this.event.preventDefault();

    if (this.item.isExpanded()) {
      this.item.closeSubNav();
      // We blur then focus so that the browser announces the collapse to
      // those using screen readers and other assistive devices.
      this.elem.blur();
      this.elem.focus();
    }
    else {
      this.item.openSubNav();
    }
  }

}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/accordion/events/OnSpace.js



/**
 * OnSpace
 *
 * Event action handler class.
 */
class OnSpace_OnSpace extends EventAbstract {

  /**
   * Execute the action to the event.
   */
  exec() {
    this.event.preventDefault();

    // Do the rest of the stuff click does.
    var eventClick = new OnClick(this.item, this.event, this.target);
    eventClick.init();

    // Focus on the first element for keyboard but not clicks.
    if (this.item.isExpanded()) {
      this.getElement('firstSubnavLink').focus();
    }
  }

}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/accordion/events/OnArrowRight.js


/**
 * OnArrowRight
 *
 * Event action handler class.
 */
class OnArrowRight_OnArrowRight extends EventAbstract {

  /**
   * Execute the action to the event.
   */
  exec() {
    // Go down a level and open the SubNav.
    this.event.preventDefault();
    this.item.openSubNav();
    this.getElement('firstSubnavLink').focus();
  }
}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/accordion/events/OnArrowLeft.js



/**
 * OnArrowLeft
 *
 * Event action handler class.
 */
class OnArrowLeft extends EventAbstract {

  /**
   * Execute the action to the event.
   */
  exec() {
    // Go up a level and close the nav.
    this.event.preventDefault();

    // Previous nav parents link item to focus on.
    var node = this.getElement('parentItem');
    this.parentNav.closeSubNav();

    // If we found a previous item focus on it.
    if (node) {
      node.focus();
    }
    // Overwise do what the navigate left option does.
    else {
      var otherLeft = new OnArrowLeft_OnArrowLeft(this.item, this.event, this.target);
      otherLeft.init();
    }
  }
}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/accordion/SecondarySubNavAccordion.js

// Click handler.

// Keyboard events.










/**
 * SecondarySubNavAccordion Class
 *
 * A sub menu class for creating a menu with accordion functionality.
 */
class SecondarySubNavAccordion {

  /**
   * Initialize.
   *
   * @param {HTMLElement} element     The container wrapper for the nav.
   * @param {Object|Mixed} masterNav  The top most level navigation.
   * @param {Object|Mixed} parentNav  The parent navigation instance if this
   *                                  instance is nested.
   * @param {Object} options          A meta object of information and options.
   */
  constructor(element, masterNav, parentNav = null, options = {}) {
    // Vars.
    this.elem = element;
    this.item = element.parentNode;
    this.masterNav = masterNav;
    this.parentNav = parentNav;
    this.depth = options.depth || 1;

    // Merge in defaults.
    this.options = Object.assign({
      itemExpandedClass: 'su-secondary-nav__item--expanded'
    }, options);

    // Assign the event dispatcher and event registry.
    this.eventRegistry = this.createEventRegistry(options);
    this.dispatch = new EventHandlerDispatch(element, this);
  }

  /**
   * Creates an event registry for handling types of events.
   *
   * This registry is used by the EventHandlerDispatch class to bind and
   * execute the events in the created property key.
   *
   * @param  {Object} options Options to merge in with the defaults.
   *
   * @return {Object} A key/value registry of events and handlers.
   */
  createEventRegistry(options) {

    var registryDefaults = {
      onClick: OnClick,
      onKeydownSpace: OnSpace_OnSpace,
      onKeydownEnter: OnSpace_OnSpace,
      onKeydownHome: OnHome,
      onKeydownEnd: OnEnd,
      onKeydownTab: OnTab,
      onKeydownEscape: OnEsc,
      onKeydownArrowUp: OnArrowUp,
      onKeydownArrowRight: OnArrowRight_OnArrowRight,
      onKeydownArrowDown: OnArrowDown,
      onKeydownArrowLeft: OnArrowLeft
    };

    return Object.assign(registryDefaults, options.eventRegistry);
  }

  /**
   * Is this expanded? Can only return TRUE if this is a subnav trigger.
   *
   * @return {Boolean}
   *  Wether or not the item is expanded.
   */
  isExpanded() {
    return this.elem.getAttribute('aria-expanded') === 'true';
  }

  /**
   * Handles the opening of a sub-nav.
   *
   * If this is a subnav trigger, open the corresponding subnav.
   * Optionally force focus on the first element in the subnav
   * (for keyboard nav).
   */
  openSubNav() {
    this.elem.setAttribute('aria-expanded', 'true');
    this.item.classList.add(this.options.itemExpandedClass);
  }

  /**
   * Handles the closing of a subnav.
   *
   * If this is a subnav trigger or an item in a subnav, close the
   * corresponding subnav. Optionally force focus on the trigger.
   */
  closeSubNav() {
    this.elem.setAttribute('aria-expanded', 'false');
    this.item.classList.remove(this.options.itemExpandedClass);
  }

  /**
   * Get the level of nesting for this nav.
   *
   * @return {Integer} The integer of depth starting at 1.
   */
  getDepth() {
    return this.depth;
  }

}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/accordion/SecondaryNavAccordion.js




/**
 * A secondary menu with accordion buttons.
 */
class SecondaryNavAccordion extends SecondaryNavAbstract {

  /**
   * Initialize.
   *
   * @param {HTMLElement} elem  The outermost wrapper for the Navigation.
   * @param {Object} options    An object of metadata.
   */
  constructor(elem, options = {}) {
    // Let super do what super does.
    super(elem, options);

    // Ok do the creation.
    this.createSubNavItems();

    // Expand the active path.
    this.activePath.expandActivePath();
  }

  /**
   * Add the additional state handling after the abstract option has run.
   *
   * @param  {HTMLElement} item The HTMLElement being acted upon.
   */
  expandActivePathItem(item) {
    item.firstElementChild.setAttribute('aria-expanded', 'true');
  }

  /**
   * Function for creating a new nested navigation item.
   *
   * @param  {HTMLElement} item     The HTMLElement to attach a new subnav to.
   * @param  {Integer} depth        The level of nesting. (starts at 1)
   * @param  {Object|Mixed} parent  The parent subnav instance.
   *
   * @return {SecondarySubNavAccordion} A brand new instance.
   */
  newParentItem(item, depth, parent) {
    var opts = this.options;
    opts.depth = depth;

    var nav = new SecondarySubNavAccordion(
      item,
      this,
      parent,
      opts
    );
    this.subNavItems.push(nav);
    return nav;
  }

  /**
   * Function for creating a new single tier navigation item.
   *
   * @param  {HTMLElement} item     The HTMLElement to attach a new subnav to.
   * @param  {Integer} depth        The level of nesting. (starts at 1)
   * @param  {Object|Mixed} parent  The parent subnav instance.
   *
   * @return {SecondaryNavItem} A brand new instance.
   */
  newNavItem(item, depth, parent) {
    var opts = this.options;
    opts.depth = depth;

    var nav = new SecondaryNavItem(
      item,
      this,
      parent,
      opts
    );
    this.navItems.push(nav);
    return nav;
  }
}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/secondary-nav-accordion.js




document.addEventListener('DOMContentLoaded', event => {

  // Process each secondary nav accordion.
  secondaryNavs.forEach((nav, index) => {
    if (nav.className.match(/su-secondary-nav--accordion/)) {
      new SecondaryNavAccordion(nav);
    }
  });

});

;// ./node_modules/decanter/core/src/js/components/secondary-nav/buttons/events/SubNavToggleClick.js


/**
 * SubNavToggleClick
 *
 * Event action handler class.
 */
class SubNavToggleClick extends EventAbstract {

  /**
   * Execute the action to the event.
   */
  exec() {
    if (this.parentNav.isExpanded()) {
      this.parentNav.closeSubNav();
      this.elem.blur();
      this.elem.focus();
    }
    else {
      this.parentNav.openSubNav();
    }
  }

}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/buttons/events/SubNavToggleSpace.js



/**
 * SubNavToggleSpace
 *
 * Event action handler class.
 */
class SubNavToggleSpace extends EventAbstract {

  /**
   * Execute the action to the event.
   */
  exec() {
    // No jumping around.
    this.event.preventDefault();

    // Call the click because it is pretty much the same thing.
    var eventClick = new SubNavToggleClick(this.item, this.event, this.target);
    eventClick.init();

    // Only focus on keyboard nav not on click.
    if (this.parentNav.isExpanded()) {
      var node = this.getElement('firstSubnavLink');
      if (node) {
        node.focus();
      }
    }
  }
}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/buttons/events/SubNavToggleArrowDown.js


/**
 * SubNavToggleArrowDown
 *
 * Event action handler class.
 */
class SubNavToggleArrowDown extends EventAbstract {

  /**
   * Execute the action to the event.
   */
  exec() {
    this.event.preventDefault();

    // If on the toggle item and the menu is expanded go down in to the first
    // menu item link as the focus.
    if (this.parentNav.isExpanded()) {
      event.stopPropagation();
      event.preventDefault();
      this.getElement('firstSubnavLink').focus();
    }
    // If current focus is on the toggle and the menu is not open, go to the
    // next sibling menu item.
    else {
      var node =
        this.getElement('next') ||
        this.getElement('parentNavNext') ||
        this.getElement('last');
      if (node) {
        node.focus();
      }
    }
  }
}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/buttons/events/SubNavToggleArrowLeft.js


/**
 * SubNavToggleArrowLeft
 *
 * Event action handler class.
 */
class SubNavToggleArrowLeft extends EventAbstract {

  /**
   * Execute the action to the event.
   */
  exec() {
    event.stopPropagation();
    event.preventDefault();
    this.parentNav.elem.focus();
  }
}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/buttons/events/SubNavToggleArrowUp.js


/**
 * SubNavToggleArrowUp
 *
 * Event action handler class.
 */
class SubNavToggleArrowUp extends EventAbstract {

  /**
   * Execute the action to the event.
   */
  exec() {
    this.event.preventDefault();

    // If the current focus is on the toggle and the menu is expanded, close
    // this nav menu and go to the parent list item.
    if (this.parentNav.isExpanded()) {
      event.stopPropagation();
      event.preventDefault();
      this.parentNav.closeSubNav();
      this.getElement('parentItem').focus();
    }
    // If the focus is on the toggle and the menu is not expanded, go to the
    // previous sibling item by calling the super method.
    else {
      var node =
        this.getElement('prev') ||
        this.getElement('parentNavPrev') ||
        this.getElement('first');
      if (node) {
        node.focus();
      }
    }
  }
}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/buttons/SubNavToggle.js

// Events










/**
 * A stoggle button.
 */
class SubNavToggle {

  /**
   * Initialize.
   *
   * @param {HTMLElement} element   The element to bind to.
   * @param {Object|Mixed} item     The parent nav instance.
   * @param {Object} options        Mixed meta information.
   */
  constructor(element, item, options) {
    this.parentNav = item;
    this.masterNav = item.masterNav;
    this.toggle = element;
    this.elem = element;
    this.options = options;

    // Assign the event dispatcher and event registry.
    this.eventRegistry = this.createEventRegistry(options);
    this.dispatch = new EventHandlerDispatch(element, this);
  }

  /**
   * Creates an event registry for handling types of events.
   *
   * This registry is used by the EventHandlerDispatch class to bind and
   * execute the events in the created property key.
   *
   * @param  {Object} options Options to merge in with the defaults.
   *
   * @return {Object} A key/value registry of events and handlers.
   */
  createEventRegistry(options) {

    var registryDefaults = {
      onClick: SubNavToggleClick,
      onKeydownSpace: SubNavToggleSpace,
      onKeydownEnter: SubNavToggleSpace,
      onKeydownHome: OnHome,
      onKeydownEnd: OnEnd,
      onKeydownEscape: OnEsc,
      onKeydownArrowUp: SubNavToggleArrowUp,
      onKeydownArrowRight: SubNavToggleSpace,
      onKeydownArrowDown: SubNavToggleArrowDown,
      onKeydownArrowLeft: SubNavToggleArrowLeft
    };

    return Object.assign(registryDefaults, options.eventRegistry);
  }
}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/buttons/events/OnTab.js


/**
 * OnTab
 *
 * Event action handler class.
 */
class OnTab_OnTab extends EventAbstract {

  /**
   * Execute the action to the event.
   */
  exec() {
    // Only act on backwards options as we want to allow the tab to go
    // to the toggle.
    const shifted = event.shiftKey;
    if (!shifted) {
      if (!this.getElement('nextElement') && this.item.getDepth() === 1) {
        this.masterNav.closeAllSubNavs();
      }
      return;
    }

    // If no previous element we are going up a level and should close
    // up behind us.
    let node = this.getElement('prev');
    if (!node) {
      this.parentNav.closeSubNav();
    }
  }
}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/buttons/events/OnArrowRight.js


/**
 * OnArrowRight
 *
 * Event action handler class.
 */
class events_OnArrowRight_OnArrowRight extends EventAbstract {

  /**
   * Execute the action to the event.
   */
  exec() {
    this.item.toggleElement.focus();
  }
}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/buttons/SecondarySubNavButtons.js

// Events

// Keyboard events.










/**
 * SecondarySubNavButtons Class
 *
 * A sub menu class for creating a menu with toggle button functionality.
 */
class SecondarySubNavButtons {

  /**
   * Initialize.
   *
   * @param {HTMLElement} element     The container wrapper for the nav.
   * @param {Object|Mixed} masterNav  The top most level navigation.
   * @param {Object|Mixed} parentNav  The parent navigation instance if this
   *                                  instance is nested.
   * @param {Object} options          A meta object of information and options.
   */
  constructor(element, masterNav, parentNav = null, options = {}) {
    // Vars.
    this.elem = element;
    this.item = element.parentNode;
    this.masterNav = masterNav;
    this.parentNav = parentNav;
    this.depth = options.depth || 1;

    // Merge in defaults.
    this.options = Object.assign({
      itemExpandedClass: 'su-secondary-nav__item--expanded',
      toggleClass: 'su-nav-toggle',
      toggleLabel: 'expand menu',
      subNavToggleText: '+'
    }, options);

    // Assign the event dispatcher and event registry.
    this.eventRegistry = this.createEventRegistry(options);
    this.dispatch = new EventHandlerDispatch(element, this);

    // Create the toggle buttons.
    this.toggleElement = this.createToggleButton();
    this.item.insertBefore(this.toggleElement, this.item.querySelector('ul'));
    this.toggle = new SubNavToggle(this.toggleElement, this, options);
  }

  /**
   * Creates an event registry for handling types of events.
   *
   * This registry is used by the EventHandlerDispatch class to bind and
   * execute the events in the created property key.
   *
   * @param  {Object} options Options to merge in with the defaults.
   *
   * @return {Object} A key/value registry of events and handlers.
   */
  createEventRegistry(options) {

    var registryDefaults = {
      onKeydownSpace: OnSpace,
      onKeydownEnter: OnSpace,
      onKeydownHome: OnHome,
      onKeydownEnd: OnEnd,
      onKeydownTab: OnTab_OnTab,
      onKeydownEscape: OnEsc,
      onKeydownArrowUp: OnArrowUp,
      onKeydownArrowRight: events_OnArrowRight_OnArrowRight,
      onKeydownArrowDown: OnArrowDown,
      onKeydownArrowLeft: OnArrowLeft_OnArrowLeft
    };

    return Object.assign(registryDefaults, options.eventRegistry);
  }

  /**
   * Create and a button for the expand/collapse actions.
   *
   * @return {HTMLElement} The button toggle.
   */
  createToggleButton() {
    let element = document.createElement('button');
    let label = document.createTextNode(this.options.toggleText);

    // Give this instance a unique ID.
    let id = 'toggle-' + Math.random().toString(36).substr(2, 9);

    element.setAttribute('class', this.options.toggleClass);
    element.setAttribute('aria-expanded', 'false');
    // element.setAttribute('aria-controls', this.subNav.id);
    element.setAttribute('aria-label', this.options.toggleLabel);
    element.setAttribute('id', id);
    element.appendChild(label);

    return element;
  }

  /**
   * Is this expanded? Can only return TRUE if this is a subnav trigger.
   *
   * @return {Boolean}
   *  Wether or not the item is expanded.
   */
  isExpanded() {
    return this.toggleElement.getAttribute('aria-expanded') === 'true';
  }

  /**
   * Handles the opening of a sub-nav.
   *
   * If this is a subnav trigger, open the corresponding subnav.
   * Optionally force focus on the first element in the subnav
   * (for keyboard nav).
   */
  openSubNav() {
    this.toggleElement.setAttribute('aria-expanded', true);
    this.item.classList.add(this.options.itemExpandedClass);
  }

  /**
   * Handles the closing of a subnav.
   *
   * If this is a subnav trigger or an item in a subnav, close the
   * corresponding subnav. Optionally force focus on the trigger.
   */
  closeSubNav() {
    this.toggleElement.setAttribute('aria-expanded', false);
    this.item.classList.remove(this.options.itemExpandedClass);
  }

  /**
   * Get the level of nesting for this nav.
   *
   * @return {Integer} The integer of depth starting at 1.
   */
  getDepth() {
    return this.depth;
  }

}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/buttons/SecondaryNavButtons.js




/**
 * A secondary menu with toggle buttons.
 */
class SecondaryNavButtons extends SecondaryNavAbstract {

  /**
   * Initialize.
   *
   * @param {HTMLElement} elem  The outermost wrapper for the Navigation.
   * @param {Object} options    An object of metadata.
   */
  constructor(elem, options = {}) {

    // Merge with the default options.
    options = Object.assign({
      itemExpandedClass: 'su-secondary-nav__item--expanded',
      toggleClass: 'su-nav-toggle',
      toggleLabel: 'expand menu',
      subNavToggleText: '+'
    }, options);

    // Call the super.
    super(elem, options);

    // Ok do the creation.
    this.createSubNavItems();

    // Expand the path.
    this.activePath.expandActivePath();
  }

  /**
   * Add the additional state handling after the abstract option has run.
   *
   * @param  {HTMLElement} item The HTMLElement being acted upon.
   */
  expandActivePathItem(item) {
    var node = item.querySelector('.' + this.options.toggleClass);
    if (node) {
      node.setAttribute('aria-expanded', 'true');
    }
  }

  /**
   * Function for creating a new nested navigation item.
   *
   * @param  {HTMLElement} item     The HTMLElement to attach a new subnav to.
   * @param  {Integer} depth        The level of nesting. (starts at 1)
   * @param  {Object|Mixed} parent  The parent subnav instance.
   *
   * @return {SecondarySubNavButtons} A brand new instance.
   */
  newParentItem(item, depth, parent) {
    var nav = new SecondarySubNavButtons(
      item,
      this,
      parent,
      {
        itemExpandedClass: this.options.itemExpandedClass,
        depth: depth
      }
    );
    this.subNavItems.push(nav);
    return nav;
  }

  /**
   * Function for creating a new single tier navigation item.
   *
   * @param  {HTMLElement} item     The HTMLElement to attach a new subnav to.
   * @param  {Integer} depth        The level of nesting. (starts at 1)
   * @param  {Object|Mixed} parent  The parent subnav instance.
   *
   * @return {SecondaryNavItem} A brand new instance.
   */
  newNavItem(item, depth, parent) {
    var nav = new SecondaryNavItem(
      item,
      this,
      parent,
      {depth: depth}
    );
    this.navItems.push(nav);
    return nav;
  }

}

;// ./node_modules/decanter/core/src/js/components/secondary-nav/secondary-nav-buttons.js




document.addEventListener('DOMContentLoaded', event => {

  secondaryNavs.forEach((nav, index) => {
    if (nav.className.match(/su-secondary-nav--buttons/)) {
      new SecondaryNavButtons(nav);
    }
  });

});

;// ./node_modules/decanter/core/src/js/components/secondary-nav/secondary-nav.js
// Get'm



;// ./node_modules/decanter/core/src/js/components/components.js
/**
 * Primary roll up file for all javascript components.
 */

// The Alert Component.

// The Accordion Component.

// The Primary Navigation Component.

// The Secondary Navigation Component.


// EXTERNAL MODULE: ./src/js/polyfills/foreach.js
var foreach = __webpack_require__(1458);
// EXTERNAL MODULE: ./node_modules/element-qsa-scope/index.js
var element_qsa_scope = __webpack_require__(2656);
;// ./src/js/polyfills/ie-edge.js
// All the polyfills.
__webpack_require__(7461);

// IE11 Fix for Element.matches
// See: https://www.npmjs.com/package/element-matches-polyfill
__webpack_require__(6497);

// Polyfill - :scope in IE/Edge.


// Polyfill for Object.assign function.
// See: https://stackoverflow.com/questions/35215360/getting-error-object-doesnt-support-property-or-method-assign
(__webpack_require__(9491).polyfill)();

// https://www.npmjs.com/package/element-closest-polyfill
__webpack_require__(6103);
;// ./src/js/polyfills/index.js
// Roll up.



;// ./src/js/components/nav/ActivePath.js
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
/**
 * ActivePath
 *
 * This class contains features and functionality for handling the finding and
 * setting of the active trail of a menu.
 */
var ActivePath_ActivePath = /*#__PURE__*/function () {
  /**
   * Initialize.
   *
   * @param {HTMLElement} element The DOM object of the navigation menu.
   * @param {Mixed} item          The Navigation Class.
   * @param {Object} options      An optional object of meta information.
   */
  function ActivePath(element, item) {
    var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
    _classCallCheck(this, ActivePath);
    this.elem = element;
    this.item = item;
    // CSS Class properties.
    this.itemActiveClass = options.itemActiveClass || 'active';
    this.itemActiveTrailClass = options.itemActiveTrailClass || 'active-trail';
    this.itemExpandedClass = options.itemExpandedClass || 'expanded';
  }

  /**
   * Dynamically add an active path to the menu tree.
   *
   * Find all links with the current window's url and add the
   * options.itemActiveClass class to the LI element container all the way up
   * the menu tree back to the root.
   */
  return _createClass(ActivePath, [{
    key: "setActivePath",
    value: function setActivePath() {
      var path = window.location.pathname;
      var anchor = window.location.hash || '';
      var query = window.location.search || '';
      var currentItem = false;

      // Queries to run to find matching active paths in order of unqiueness.
      var finders = [this.elem.querySelector("a[href*='" + anchor + "']"), this.elem.querySelector("a[href*='" + query + "']"), this.elem.querySelector("a[href='" + path + query + anchor + "']"), this.elem.querySelector("a[href*='" + path + query + "']")];

      // Go through the queries and see if we have any results.
      finders.forEach(function (val) {
        if (!currentItem && val) {
          currentItem = val;
        }
      });

      // Can't find anything. End.
      if (!currentItem) {
        return;
      }

      // While we have parents go up and add the active class.
      while (currentItem) {
        // If we are on a LI element we need to add the active class.
        if (currentItem.tagName === 'LI') {
          currentItem.classList.add(this.itemActiveClass);
          break;
        }

        // Always increment.
        currentItem = currentItem.parentNode;
      }
    }

    /**
     * Set active trail.
     *
     * After this.setActivePath() has been run or the itemActiveClass has been set
     * on all the appropriate menu items go through the nav and set the trail on
     * subNavItems that contain activeClass items.
     */
  }, {
    key: "setActiveTrail",
    value: function setActiveTrail() {
      var _this = this;
      var actives = this.elem.querySelectorAll('.' + this.itemActiveClass);
      if (actives.length) {
        actives.forEach(function (element) {
          // While we have parents go up and add the active class.
          while (element) {
            // End when we get to the parent nav item stop.
            if (element === _this.elem) {
              // Stop at the top most level.
              break;
            }

            // If we are on a LI element we need to add the active class.
            if (element.tagName === 'LI') {
              element.classList.add(_this.itemActiveTrailClass);
              // "Hook" of sorts.
              if (typeof _this.item.setActiveTrialItem == 'function') {
                _this.item.setActiveTrialItem(element);
              }
            }

            // Always increment.
            element = element.parentNode;
          }
        });
      }
    }

    /**
     * Expand all menus in the active path.
     *
     * After this.setActivePath() has been run or the itemActiveClass has been set
     * on all the appropriate menu items go through the nav and expand the
     * subNavItems that contain activeClass items.
     */
  }, {
    key: "expandActivePath",
    value: function expandActivePath() {
      var _this2 = this;
      var actives = this.elem.querySelectorAll('.' + this.itemActiveClass);
      if (actives.length) {
        actives.forEach(function (element) {
          // While we have parents go up and add the active class.
          while (element) {
            // End when we get to the parent nav item stop.
            if (element === _this2.elem) {
              // Stop at the top most level.
              break;
            }

            // If we are on a LI element we need to add the active class.
            if (element.tagName === 'LI') {
              element.classList.add(_this2.itemExpandedClass);
              // "Hook" of sorts.
              if (typeof _this2.item.expandActivePathItem == 'function') {
                _this2.item.expandActivePathItem(element);
              }
            }

            // Always increment.
            element = element.parentNode;
          }
        });
      }
    }
  }]);
}();

;// ./src/js/utilities/keyboard.js
// ---------------------------------------------------------------------------
// Keyboard helper functions
// ---------------------------------------------------------------------------

var keyboard_isHome = function isHome(theKey) {
  return theKey === 'Home' || theKey === 122;
};
var keyboard_isEnd = function isEnd(theKey) {
  return theKey === 'End' || theKey === 123;
};
var keyboard_isTab = function isTab(theKey) {
  return theKey === 'Tab' || theKey === 9;
};
var keyboard_isEsc = function isEsc(theKey) {
  return theKey === 'Escape' || theKey === 'Esc' || theKey === 27;
};
var keyboard_isSpace = function isSpace(theKey) {
  return theKey === ' ' || theKey === 'Spacebar' || theKey === 32;
};
var keyboard_isEnter = function isEnter(theKey) {
  return theKey === 'Enter' || theKey === 13;
};
var keyboard_isLeftArrow = function isLeftArrow(theKey) {
  return theKey === 'ArrowLeft' || theKey === 'Left' || theKey === 37;
};
var keyboard_isRightArrow = function isRightArrow(theKey) {
  return theKey === 'ArrowRight' || theKey === 'Right' || theKey === 39;
};
var keyboard_isUpArrow = function isUpArrow(theKey) {
  return theKey === 'ArrowUp' || theKey === 'Up' || theKey === 38;
};
var keyboard_isDownArrow = function isDownArrow(theKey) {
  return theKey === 'ArrowDown' || theKey === 'Down' || theKey === 40;
};

/**
 * Return a consistent string for each key validation.
 *
 * @param {*} theKey the code from a keypress event.
 *
 * @return {String} A string name for the key that was pressed.
 */
var keyboard_normalizeKey = function normalizeKey(theKey) {
  // Key Value Map of the normalized string and the check function.
  var map = {
    home: keyboard_isHome,
    end: keyboard_isEnd,
    tab: keyboard_isTab,
    escape: keyboard_isEsc,
    space: keyboard_isSpace,
    enter: keyboard_isEnter,
    arrowLeft: keyboard_isLeftArrow,
    arrowRight: keyboard_isRightArrow,
    arrowUp: keyboard_isUpArrow,
    arrowDown: keyboard_isDownArrow
  };

  // Loop through the key/val object and run the check function (val) in order
  // to return the normalized string (key)
  for (var _i = 0, _Object$entries = Object.entries(map); _i < _Object$entries.length; _i++) {
    var entry = _Object$entries[_i];
    if (entry[1](theKey)) {
      return entry[0];
    }
  }
  return false;
};
;// ./src/js/polyfills/createEvent.js
/**
 * Create an event with the specified name in a browser-agnostic way.
 *
 * @param {string} eventName - the name of the event
 * @param {Object} data - Additional data along with the event.
 *
 * @return {Event} - instance of event which can be dispatched / listened for
 */
var createEvent_createEvent = function createEvent(eventName, data) {
  if (typeof eventName !== 'string' || eventName.length <= 0) {
    return null;
  }
  // Modern browsers.
  if (typeof Event == 'function') {
    return new Event(eventName, data);
  }
  // IE
  else {
    var ev = document.createEvent('UIEvent');
    ev.initEvent(eventName, true, true, data);
    return ev;
  }
};
;// ./src/js/components/nav/EventHandlerDispatch.js
function EventHandlerDispatch_typeof(o) { "@babel/helpers - typeof"; return EventHandlerDispatch_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, EventHandlerDispatch_typeof(o); }
function EventHandlerDispatch_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function EventHandlerDispatch_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, EventHandlerDispatch_toPropertyKey(o.key), o); } }
function EventHandlerDispatch_createClass(e, r, t) { return r && EventHandlerDispatch_defineProperties(e.prototype, r), t && EventHandlerDispatch_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function EventHandlerDispatch_toPropertyKey(t) { var i = EventHandlerDispatch_toPrimitive(t, "string"); return "symbol" == EventHandlerDispatch_typeof(i) ? i : i + ""; }
function EventHandlerDispatch_toPrimitive(t, r) { if ("object" != EventHandlerDispatch_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != EventHandlerDispatch_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }



/**
 * EventHandlerDispatch Class
 *
 * This class provides dynamic handling of click and keyboard events and can be
 * attached to any class/HTMLElement.
 */
var EventHandlerDispatch_EventHandlerDispatch = /*#__PURE__*/function () {
  /**
   * Initialize.
   *
   * @param {HTMLElement} element   The HTMLElement to bind listeners to.
   * @param {[type]}      handler   The Javascript Class instance with the
   *                                eventRegistry property.
   */
  function EventHandlerDispatch(element, handler) {
    EventHandlerDispatch_classCallCheck(this, EventHandlerDispatch);
    this.elem = element;
    this.handler = handler;
    this.createEventListeners();
  }

  /**
   * Create new event listeners.
   */
  return EventHandlerDispatch_createClass(EventHandlerDispatch, [{
    key: "createEventListeners",
    value: function createEventListeners() {
      // What to do when a key is down?
      this.elem.addEventListener('keydown', this);

      // Listen to the click so we can act on it.
      this.elem.addEventListener('click', this);

      // Listen to custom events so we can act on it.
      this.elem.addEventListener('preOpenSubnav', this);

      // Listen to custom events so we can act on it.
      this.elem.addEventListener('postOpenSubnav', this);

      // Listen to custom events so we can act on it.
      this.elem.addEventListener('preCloseSubnav', this);

      // Listen to custom events so we can act on it.
      this.elem.addEventListener('postCloseSubnav', this);
    }

    /**
     * Handler for all events attached to an instance of this class.
     *
     * This method must exist when events are bound to an instance of a class
     * (vs a function). This method is called for all events bound to an
     * instance. It inspects the instance (this) for an appropriate handler
     * based on the event type. If found, it dispatches the event to the
     * appropriate handler.
     *
     * @param {Event} event - The triggering event.
     */
  }, {
    key: "handleEvent",
    value: function handleEvent(event) {
      event = event || window.event;

      // Create an event signature.
      var eventMethod = 'on' + event.type.charAt(0).toUpperCase() + event.type.slice(1);

      // What was clicked.
      var target = event.target || event.srcElement;
      if (eventMethod === 'onKeydown') {
        this.onKeydown(event, target);
      } else if (eventMethod === 'onClick') {
        this.onClick(event, target);
      } else {
        this.callEvent(eventMethod, event, target);
      }
    }

    /**
     * Handler for keydown events.
     *
     * @param {KeyboardEvent} event - The keyboard event object.
     * @param {HTMLElement} target  - The HTML element target.
     */
  }, {
    key: "onKeydown",
    value: function onKeydown(event, target) {
      var theKey = event.key || event.keyCode;
      var normalized = keyboard_normalizeKey(theKey);

      // We don't know or need to handle the key that was pressed.
      if (!normalized) {
        return;
      }

      // Prepare a dynamic handler.
      var eventMethod = 'onKeydown' + normalized.charAt(0).toUpperCase() + normalized.slice(1);

      // Do eet.
      this.callEvent(eventMethod, event, target);
    }

    /**
     * Handler for click events.
     *
     * @param  {Event} event  A Javascript event.
     * @param  {HTMLElement} target The target of the event.
     */
  }, {
    key: "onClick",
    value: function onClick(event, target) {
      this.callEvent('onClick', event, target);
    }

    /**
     * The event handler
     *
     * Initializes and executes an object to handle the Javascript Event as
     * defined by the handlers eventRegistry.
     *
     * @param  {String} eventMethod A string key for the eventRegistry;
     * @param  {Event} event        The Javascript event.
     * @param  {HTMLElement} target The DOM object that the event is triggered on.
     */
  }, {
    key: "callEvent",
    value: function callEvent(eventMethod, event, target) {
      // Let everyone know.
      if (this.handler.elem) {
        var dynamicEvent = createEvent_createEvent(eventMethod, {
          bubbles: true,
          data: {
            item: this.handler
          }
        });
        this.handler.elem.dispatchEvent(dynamicEvent);
      }

      // Call the specific handler.
      if (typeof this.handler.eventRegistry[eventMethod] === 'function') {
        var eventObj = new this.handler.eventRegistry[eventMethod](this.handler, event, target);
        eventObj.init();
      }
    }
  }]);
}();

;// ./src/js/components/nav/ElementFetcher.js
function ElementFetcher_typeof(o) { "@babel/helpers - typeof"; return ElementFetcher_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, ElementFetcher_typeof(o); }
function ElementFetcher_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function ElementFetcher_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, ElementFetcher_toPropertyKey(o.key), o); } }
function ElementFetcher_createClass(e, r, t) { return r && ElementFetcher_defineProperties(e.prototype, r), t && ElementFetcher_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function ElementFetcher_toPropertyKey(t) { var i = ElementFetcher_toPrimitive(t, "string"); return "symbol" == ElementFetcher_typeof(i) ? i : i + ""; }
function ElementFetcher_toPrimitive(t, r) { if ("object" != ElementFetcher_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != ElementFetcher_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
/**
 * ElementFetcher Class
 *
 * Provides a relative named DOM navigator for quickly getting elements relative
 * to the provided context.
 */
var ElementFetcher_ElementFetcher = /*#__PURE__*/function () {
  /**
   * Initialize.
   *
   * @param {HTMLElement} element   The DOM object to use.
   * @param {String} what           A named string.
   */
  function ElementFetcher(element, what) {
    ElementFetcher_classCallCheck(this, ElementFetcher);
    this.item = element;
    this.what = what;
  }

  /**
   * Attempt to retrieve an item.
   *
   * @return {Boolean|HTMLElement} An element or false if `what` is not found.
   */
  return ElementFetcher_createClass(ElementFetcher, [{
    key: "fetch",
    value: function fetch() {
      try {
        switch (this.what) {
          case 'first':
            return this.item.parentNode.firstElementChild.firstChild;
          case 'last':
            return this.item.parentNode.lastElementChild.firstChild;
          case 'firstElement':
            return this.item.parentNode.firstElementChild;
          case 'lastElement':
            return this.item.parentNode.lastElementChild;
          case 'lastToggle':
            return this.item.parentNode.lastElementChild.querySelector(':scope .su-nav-toggle');
          case 'next':
            return this.item.nextElementSibling.querySelector(':scope a');
          case 'prev':
            return this.item.previousElementSibling.querySelector(':scope a');
          case 'nextElement':
            return this.item.nextElementSibling;
          case 'prevElement':
            return this.item.previousElementSibling;
          case 'prevToggle':
            return this.item.previousElementSibling.querySelector(':scope .su-nav-toggle');
          case 'prevElementSiblingSubnavLast':
            return this.item.previousElementSibling.querySelector(':scope > ul li a:last-child');
          case 'parentItem':
            var node = this.item.parentNode.parentNode;
            if (node.tagName === 'NAV') {
              return false;
            }
            return node.querySelector('a');
          case 'parentButton':
            return this.item.parentNode.parentNode.querySelector('button');
          case 'parentNav':
            return this.item.parentNode.parentNode;
          case 'parentNavLast':
            return this.item.parentNode.parentNode.parentNode.lastElementChild.querySelector('a');
          case 'parentNavFirst':
            return this.item.parentNode.parentNode.parentNode.firstElementChild.querySelector('a');
          case 'parentNavNext':
            return this.item.parentNode.parentNode.nextElementSibling;
          case 'parentNavNextItem':
            return this.item.parentNode.parentNode.nextElementSibling.querySelector('a');
          case 'parentNavPrev':
            return this.item.parentNode.parentNode.previousElementSibling;
          case 'parentNavPrevItem':
            return this.item.parentNode.parentNode.previousElementSibling.querySelector('a');
          case 'firstSubnavLink':
            return this.item.querySelector(':scope > ul li a');
          case 'firstSubnavItem':
            return this.item.querySelector(':scope > ul li');
          case 'subnav':
            return this.item.querySelector(':scope > ul');
          default:
            return false;
        }
      } catch (err) {
        return false;
      }
    }
  }]);
}();

;// ./src/js/components/secondary-nav/common/events/EventAbstract.js
function EventAbstract_typeof(o) { "@babel/helpers - typeof"; return EventAbstract_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, EventAbstract_typeof(o); }
function EventAbstract_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function EventAbstract_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, EventAbstract_toPropertyKey(o.key), o); } }
function EventAbstract_createClass(e, r, t) { return r && EventAbstract_defineProperties(e.prototype, r), t && EventAbstract_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function EventAbstract_toPropertyKey(t) { var i = EventAbstract_toPrimitive(t, "string"); return "symbol" == EventAbstract_typeof(i) ? i : i + ""; }
function EventAbstract_toPrimitive(t, r) { if ("object" != EventAbstract_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != EventAbstract_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }


/**
 * EventAbstract
 *
 * An abstract class for creating an interface for working with the
 * EventHandlerDispatch class. This is the signature for all instances
 * that are evoked through the eventRegistry.
 */
var EventAbstract_EventAbstract = /*#__PURE__*/function () {
  /**
   * Initialize.
   *
   * @param {Object|Mixed} item The javascript object instance that this is bound to.
   * @param {KeyboardEvent|MouseEvent} event - The event object.
   * @param {HTMLElement} target  - The HTML element target.
   */
  function EventAbstract(item, event, target) {
    EventAbstract_classCallCheck(this, EventAbstract);
    this.item = item;
    this.elem = item.elem;
    this.masterNav = item.masterNav;
    this.parentNav = item.parentNav;
    this.target = target;
    this.event = event;
  }

  /**
   * A validation shorcut that should pass before running exec().
   *
   * @return {Boolean} Wether or not the event target is what this instance is bound to.
   */
  return EventAbstract_createClass(EventAbstract, [{
    key: "isOnTarget",
    value: function isOnTarget() {
      // Check to see if the event target is what this instance is bound to.
      if (this.target === this.elem) {
        return true;
      }
      return false;
    }

    /**
     * A validation method that should pass before running exec().
     *
     * @return {Boolean} Wether or not validation passes.
     */
  }, {
    key: "validate",
    value: function validate() {
      // Only act on me.
      if (!this.isOnTarget()) {
        return false;
      }
      return true;
    }

    /**
     * Interface method.
     *
     * When evoking this abstract instance you should use this method as your
     * iterface for calling the action.
     */
  }, {
    key: "init",
    value: function init() {
      if (this.validate()) {
        this.exec();
      }
    }

    /**
     * Shortcut function to find a DOM element.
     *
     * This is a helper function that uses a ElementFetcher instance to navigate
     * and traverse the DOM relative to the current context.
     *
     * @param  {String} what A keyword for what we are trying to find.
     * @param  {HTMLElement} context The relative starting location for the finder.
     * @return {Boolean|HTMLElement} False if not found or an HTMLElement.
     */
  }, {
    key: "getElement",
    value: function getElement(what) {
      var context = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : this.elem.parentNode;
      var fetcher = new ElementFetcher_ElementFetcher(context, what);
      return fetcher.fetch();
    }

    /**
     * Check the screen width to determine if mobile or not.
     * @return {Boolean} [description]
     */
  }, {
    key: "isDesktop",
    value: function isDesktop() {
      // 992 is the LG breakpoint.
      if (window.innerWidth >= 992) {
        return true;
      }
      return false;
    }
  }]);
}();

;// ./src/js/components/secondary-nav/common/events/OnEsc.js
function OnEsc_typeof(o) { "@babel/helpers - typeof"; return OnEsc_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, OnEsc_typeof(o); }
function OnEsc_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function OnEsc_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, OnEsc_toPropertyKey(o.key), o); } }
function OnEsc_createClass(e, r, t) { return r && OnEsc_defineProperties(e.prototype, r), t && OnEsc_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function OnEsc_toPropertyKey(t) { var i = OnEsc_toPrimitive(t, "string"); return "symbol" == OnEsc_typeof(i) ? i : i + ""; }
function OnEsc_toPrimitive(t, r) { if ("object" != OnEsc_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != OnEsc_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _callSuper(t, o, e) { return o = _getPrototypeOf(o), _possibleConstructorReturn(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], _getPrototypeOf(t).constructor) : o.apply(t, e)); }
function _possibleConstructorReturn(t, e) { if (e && ("object" == OnEsc_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return _assertThisInitialized(t); }
function _assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _getPrototypeOf(t) { return _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, _getPrototypeOf(t); }
function _inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && _setPrototypeOf(t, e); }
function _setPrototypeOf(t, e) { return _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, _setPrototypeOf(t, e); }



/**
 * OnEsc
 *
 * Event action handler class.
 */
var OnEsc_OnEsc = /*#__PURE__*/function (_EventAbstract) {
  function OnEsc() {
    OnEsc_classCallCheck(this, OnEsc);
    return _callSuper(this, OnEsc, arguments);
  }
  _inherits(OnEsc, _EventAbstract);
  return OnEsc_createClass(OnEsc, [{
    key: "exec",
    value:
    /**
     * Execute the action to the event.
     */
    function exec() {
      this.event.preventDefault();
      var node = false;
      if (this.parentNav.getDepth() > 2) {
        this.event.stopPropagation();
        this.parentNav.closeSubNav();
        node = this.getElement('parentItem').parentElement.getElementsByTagName('button')[0];
      } else {
        if (this.isDesktop()) {
          this.masterNav.closeAllSubNavs();
          node = this.elem;
        } else {
          var closeAllEvent = createEvent_createEvent('closeAllMobileNavs', {
            bubbles: true,
            data: this.item
          });
          this.elem.dispatchEvent(closeAllEvent);
        }
      }
      if (node) {
        node.focus();
      }
    }
  }]);
}(EventAbstract_EventAbstract);

;// ./src/js/components/secondary-nav/common/events/OnSpace.js
function OnSpace_typeof(o) { "@babel/helpers - typeof"; return OnSpace_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, OnSpace_typeof(o); }
function OnSpace_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function OnSpace_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, OnSpace_toPropertyKey(o.key), o); } }
function OnSpace_createClass(e, r, t) { return r && OnSpace_defineProperties(e.prototype, r), t && OnSpace_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function OnSpace_toPropertyKey(t) { var i = OnSpace_toPrimitive(t, "string"); return "symbol" == OnSpace_typeof(i) ? i : i + ""; }
function OnSpace_toPrimitive(t, r) { if ("object" != OnSpace_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != OnSpace_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function OnSpace_callSuper(t, o, e) { return o = OnSpace_getPrototypeOf(o), OnSpace_possibleConstructorReturn(t, OnSpace_isNativeReflectConstruct() ? Reflect.construct(o, e || [], OnSpace_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function OnSpace_possibleConstructorReturn(t, e) { if (e && ("object" == OnSpace_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return OnSpace_assertThisInitialized(t); }
function OnSpace_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function OnSpace_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (OnSpace_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function OnSpace_getPrototypeOf(t) { return OnSpace_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, OnSpace_getPrototypeOf(t); }
function OnSpace_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && OnSpace_setPrototypeOf(t, e); }
function OnSpace_setPrototypeOf(t, e) { return OnSpace_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, OnSpace_setPrototypeOf(t, e); }


/**
 * OnSpace
 *
 * Event action handler class.
 */
var events_OnSpace_OnSpace = /*#__PURE__*/function (_EventAbstract) {
  function OnSpace() {
    OnSpace_classCallCheck(this, OnSpace);
    return OnSpace_callSuper(this, OnSpace, arguments);
  }
  OnSpace_inherits(OnSpace, _EventAbstract);
  return OnSpace_createClass(OnSpace, [{
    key: "exec",
    value:
    /**
     * Execute the action to the event.
     */
    function exec() {
      this.event.stopPropagation();
      this.event.preventDefault();
      window.location = this.target.getAttribute('href');
    }
  }]);
}(EventAbstract_EventAbstract);

;// ./src/js/components/secondary-nav/common/SecondaryNavAbstract.js
function SecondaryNavAbstract_typeof(o) { "@babel/helpers - typeof"; return SecondaryNavAbstract_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, SecondaryNavAbstract_typeof(o); }
function SecondaryNavAbstract_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function SecondaryNavAbstract_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, SecondaryNavAbstract_toPropertyKey(o.key), o); } }
function SecondaryNavAbstract_createClass(e, r, t) { return r && SecondaryNavAbstract_defineProperties(e.prototype, r), t && SecondaryNavAbstract_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function SecondaryNavAbstract_toPropertyKey(t) { var i = SecondaryNavAbstract_toPrimitive(t, "string"); return "symbol" == SecondaryNavAbstract_typeof(i) ? i : i + ""; }
function SecondaryNavAbstract_toPrimitive(t, r) { if ("object" != SecondaryNavAbstract_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != SecondaryNavAbstract_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }





/**
 * SecondaryNav Class
 *
 * The most abstract version of a SecondaryNav. All Nav types should extend
 * this class in order to have a psuedo interface and default methods.
 */
var SecondaryNavAbstract_SecondaryNavAbstract = /*#__PURE__*/function () {
  /**
   * Nav Abstract Constructor class.
   *
   * @param {HTMLElement} element    The html element to use as the parent for the nav list.
   * @param {Object} options      An object with key value pairs of configuration options.
   */
  function SecondaryNavAbstract(element) {
    var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    SecondaryNavAbstract_classCallCheck(this, SecondaryNavAbstract);
    // What HTML element this is bound to.
    this.elem = element;

    // Set some default options.
    var defaultOptions = {
      itemClass: 'su-secondary-nav__item',
      itemExpandedClass: 'su-secondary-nav__item--expanded',
      itemActiveClass: 'su-secondary-nav__item--current',
      itemActiveTrailClass: 'su-secondary-nav__item--active-trail',
      itemParentClass: 'su-secondary-nav__item--parent',
      eventRegistry: {},
      activeTrail: true,
      expand: true
    };

    // Merge with passed in options.
    this.options = Object.assign(defaultOptions, options);

    // Remove the no-js class and add an ID.
    this.elem.classList.remove('no-js');
    if (!this.elem.getAttribute('id')) {
      this.id = 'su-nav-id-' + Math.random().toString(36).substr(2, 9);
      this.elem.setAttribute('id', this.id);
    }

    // Assign the event dispatcher and event registry.
    this.eventRegistry = this.createEventRegistry(options);
    this.dispatch = new EventHandlerDispatch_EventHandlerDispatch(element, this);

    // Handle the active state.
    // Optionally set the trail.
    if (this.options.activeTrail) {
      this.activePath = new ActivePath_ActivePath(element, this, this.options);
      this.activePath.setActivePath();
      this.activePath.setActiveTrail();
    }

    // Helper Item Variables.
    this.navItems = [];
    this.subNavItems = [];
    this.parentItemSelector = ':scope > ul > .' + this.options.itemParentClass;
    this.navItemSelector = ':scope > ul > .' + this.options.itemClass + ':not(.' + this.options.itemParentClass + ')';
  }

  /**
   * Add the additional state handling after the abstract option has run.
   *
   * @param  {HTMLElement} item The HTMLElement being acted upon.
   */
  return SecondaryNavAbstract_createClass(SecondaryNavAbstract, [{
    key: "expandActivePathItem",
    value: function expandActivePathItem(item) {
      // For any additional items outside of the core functions.
    }

    /**
     * Add anything additional after the abstract option has run.
     *
     * @param  {HTMLElement} item The HTMLElement being acted upon.
     */
  }, {
    key: "setActivePathItem",
    value: function setActivePathItem(item) {
      // For any additional items outside of the core functions.
    }

    /**
     * Creates an event registry for handling types of events.
     *
     * This registry is used by the EventHandlerDispatch class to bind and
     * execute the events in the created property key.
     *
     * @param  {Object} options Options to merge in with the defaults.
     *
     * @return {Object} A key/value registry of events and handlers.
     */
  }, {
    key: "createEventRegistry",
    value: function createEventRegistry(options) {
      var registryDefaults = {
        onKeydownEscape: OnEsc_OnEsc,
        onKeydownSpace: events_OnSpace_OnSpace
      };
      return Object.assign(registryDefaults, options.eventRegistry);
    }

    /**
     * Kickoff method for generating single and multi-tier nav instances.
     */
  }, {
    key: "createSubNavItems",
    value: function createSubNavItems() {
      // Find all the single and multi-tier items.
      var parentItems = this.elem.querySelectorAll(this.parentItemSelector);
      var leafItems = this.elem.querySelectorAll(this.navItemSelector);

      // Sub Nav Items.
      if (parentItems.length >= 1) {
        this.createParentItems(parentItems);
      }

      // Regular Ol Items.
      if (leafItems.length >= 1) {
        this.createNavItems(leafItems);
      }
    }

    /**
     * Recursive loop for creating nested navigation instances.
     *
     * @param  {NodeList} items A set of sibling parent menu items.
     * @param  {Number} depth The current depth of recursion.
     * @param  {Object|Mixed} parentMenu The instance of the parent menu.
     */
  }, {
    key: "createParentItems",
    value: function createParentItems(items) {
      var _this = this;
      var depth = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 1;
      var parentMenu = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
      items.forEach(function (item) {
        var itemLink = item.querySelector('a');
        var parentItems = item.querySelectorAll(_this.parentItemSelector);
        var leafItems = item.querySelectorAll(_this.navItemSelector);
        var nextDepth = depth + 1;
        var parentNav = null;

        // If we have a link add to it.
        if (itemLink) {
          parentNav = _this.newParentItem(itemLink, depth, parentMenu);
        }

        // Nested Sub Nav Items.
        if (parentItems.length >= 1) {
          _this.createParentItems(parentItems, nextDepth, parentNav);
        }

        // Nested Nav Items.
        if (leafItems.length >= 1) {
          _this.createNavItems(leafItems, nextDepth, parentNav);
        }
      });
    }

    /**
     * Recursive loop for creating single level navigation instances.
     *
     * @param  {NodeList} items A set of sibling parent menu items.
     * @param  {Number} depth The current depth of recursion.
     * @param  {Object|Mixed} parentMenu The instance of the parent menu.
     */
  }, {
    key: "createNavItems",
    value: function createNavItems(items) {
      var _this2 = this;
      var depth = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 1;
      var parentMenu = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
      items.forEach(function (item) {
        var itemLink = item.querySelector('a');
        if (itemLink) {
          _this2.newNavItem(itemLink, depth, parentMenu);
        }
      });
    }

    /**
     * Close all subNavItems in this Nav.
     */
  }, {
    key: "closeAllSubNavs",
    value: function closeAllSubNavs() {
      this.subNavItems.forEach(function (item, event) {
        item.closeSubNav();
      });
    }

    /**
     * Close only this subnav.
     */
  }, {
    key: "closeSubNav",
    value: function closeSubNav() {
      this.closeAllSubNavs();
    }
  }]);
}();

;// ./src/js/components/secondary-nav/common/events/OnHome.js
function OnHome_typeof(o) { "@babel/helpers - typeof"; return OnHome_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, OnHome_typeof(o); }
function OnHome_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function OnHome_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, OnHome_toPropertyKey(o.key), o); } }
function OnHome_createClass(e, r, t) { return r && OnHome_defineProperties(e.prototype, r), t && OnHome_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function OnHome_toPropertyKey(t) { var i = OnHome_toPrimitive(t, "string"); return "symbol" == OnHome_typeof(i) ? i : i + ""; }
function OnHome_toPrimitive(t, r) { if ("object" != OnHome_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != OnHome_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function OnHome_callSuper(t, o, e) { return o = OnHome_getPrototypeOf(o), OnHome_possibleConstructorReturn(t, OnHome_isNativeReflectConstruct() ? Reflect.construct(o, e || [], OnHome_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function OnHome_possibleConstructorReturn(t, e) { if (e && ("object" == OnHome_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return OnHome_assertThisInitialized(t); }
function OnHome_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function OnHome_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (OnHome_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function OnHome_getPrototypeOf(t) { return OnHome_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, OnHome_getPrototypeOf(t); }
function OnHome_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && OnHome_setPrototypeOf(t, e); }
function OnHome_setPrototypeOf(t, e) { return OnHome_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, OnHome_setPrototypeOf(t, e); }


/**
 * OnHome
 *
 * Event action handler class.
 */
var OnHome_OnHome = /*#__PURE__*/function (_EventAbstract) {
  function OnHome() {
    OnHome_classCallCheck(this, OnHome);
    return OnHome_callSuper(this, OnHome, arguments);
  }
  OnHome_inherits(OnHome, _EventAbstract);
  return OnHome_createClass(OnHome, [{
    key: "exec",
    value:
    /**
     * Execute the action to the event.
     */
    function exec() {
      this.event.preventDefault();
      var node = this.getElement('first');
      if (node) {
        node.focus();
      }
    }
  }]);
}(EventAbstract_EventAbstract);

;// ./src/js/components/secondary-nav/common/events/OnArrowDown.js
function OnArrowDown_typeof(o) { "@babel/helpers - typeof"; return OnArrowDown_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, OnArrowDown_typeof(o); }
function OnArrowDown_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function OnArrowDown_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, OnArrowDown_toPropertyKey(o.key), o); } }
function OnArrowDown_createClass(e, r, t) { return r && OnArrowDown_defineProperties(e.prototype, r), t && OnArrowDown_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function OnArrowDown_toPropertyKey(t) { var i = OnArrowDown_toPrimitive(t, "string"); return "symbol" == OnArrowDown_typeof(i) ? i : i + ""; }
function OnArrowDown_toPrimitive(t, r) { if ("object" != OnArrowDown_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != OnArrowDown_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function OnArrowDown_callSuper(t, o, e) { return o = OnArrowDown_getPrototypeOf(o), OnArrowDown_possibleConstructorReturn(t, OnArrowDown_isNativeReflectConstruct() ? Reflect.construct(o, e || [], OnArrowDown_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function OnArrowDown_possibleConstructorReturn(t, e) { if (e && ("object" == OnArrowDown_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return OnArrowDown_assertThisInitialized(t); }
function OnArrowDown_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function OnArrowDown_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (OnArrowDown_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function OnArrowDown_getPrototypeOf(t) { return OnArrowDown_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, OnArrowDown_getPrototypeOf(t); }
function OnArrowDown_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && OnArrowDown_setPrototypeOf(t, e); }
function OnArrowDown_setPrototypeOf(t, e) { return OnArrowDown_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, OnArrowDown_setPrototypeOf(t, e); }



/**
 * OnArrowDown
 *
 * Event action handler class.
 */
var OnArrowDown_OnArrowDown = /*#__PURE__*/function (_EventAbstract) {
  function OnArrowDown() {
    OnArrowDown_classCallCheck(this, OnArrowDown);
    return OnArrowDown_callSuper(this, OnArrowDown, arguments);
  }
  OnArrowDown_inherits(OnArrowDown, _EventAbstract);
  return OnArrowDown_createClass(OnArrowDown, [{
    key: "exec",
    value:
    /**
     * Execute the action to the event.
     */
    function exec() {
      this.event.preventDefault();

      // Go to the next item.
      var node = this.getElement('next');
      if (node) {
        node.focus();
        return;
      }

      // If a node is not found go to home.
      var eventHome = new OnHome_OnHome(this.item, this.event, this.target);
      eventHome.init();
    }
  }]);
}(EventAbstract_EventAbstract);

;// ./src/js/components/secondary-nav/common/events/OnEnd.js
function OnEnd_typeof(o) { "@babel/helpers - typeof"; return OnEnd_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, OnEnd_typeof(o); }
function OnEnd_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function OnEnd_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, OnEnd_toPropertyKey(o.key), o); } }
function OnEnd_createClass(e, r, t) { return r && OnEnd_defineProperties(e.prototype, r), t && OnEnd_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function OnEnd_toPropertyKey(t) { var i = OnEnd_toPrimitive(t, "string"); return "symbol" == OnEnd_typeof(i) ? i : i + ""; }
function OnEnd_toPrimitive(t, r) { if ("object" != OnEnd_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != OnEnd_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function OnEnd_callSuper(t, o, e) { return o = OnEnd_getPrototypeOf(o), OnEnd_possibleConstructorReturn(t, OnEnd_isNativeReflectConstruct() ? Reflect.construct(o, e || [], OnEnd_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function OnEnd_possibleConstructorReturn(t, e) { if (e && ("object" == OnEnd_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return OnEnd_assertThisInitialized(t); }
function OnEnd_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function OnEnd_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (OnEnd_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function OnEnd_getPrototypeOf(t) { return OnEnd_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, OnEnd_getPrototypeOf(t); }
function OnEnd_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && OnEnd_setPrototypeOf(t, e); }
function OnEnd_setPrototypeOf(t, e) { return OnEnd_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, OnEnd_setPrototypeOf(t, e); }


/**
 * OnEnd
 *
 * Event action handler class.
 */
var OnEnd_OnEnd = /*#__PURE__*/function (_EventAbstract) {
  function OnEnd() {
    OnEnd_classCallCheck(this, OnEnd);
    return OnEnd_callSuper(this, OnEnd, arguments);
  }
  OnEnd_inherits(OnEnd, _EventAbstract);
  return OnEnd_createClass(OnEnd, [{
    key: "exec",
    value:
    /**
     * Execute the action to the event.
     */
    function exec() {
      this.event.preventDefault();
      var node = this.getElement('last');
      if (node) {
        node.focus();
      }
    }
  }]);
}(EventAbstract_EventAbstract);

;// ./src/js/components/secondary-nav/common/events/OnArrowUp.js
function OnArrowUp_typeof(o) { "@babel/helpers - typeof"; return OnArrowUp_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, OnArrowUp_typeof(o); }
function OnArrowUp_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function OnArrowUp_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, OnArrowUp_toPropertyKey(o.key), o); } }
function OnArrowUp_createClass(e, r, t) { return r && OnArrowUp_defineProperties(e.prototype, r), t && OnArrowUp_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function OnArrowUp_toPropertyKey(t) { var i = OnArrowUp_toPrimitive(t, "string"); return "symbol" == OnArrowUp_typeof(i) ? i : i + ""; }
function OnArrowUp_toPrimitive(t, r) { if ("object" != OnArrowUp_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != OnArrowUp_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function OnArrowUp_callSuper(t, o, e) { return o = OnArrowUp_getPrototypeOf(o), OnArrowUp_possibleConstructorReturn(t, OnArrowUp_isNativeReflectConstruct() ? Reflect.construct(o, e || [], OnArrowUp_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function OnArrowUp_possibleConstructorReturn(t, e) { if (e && ("object" == OnArrowUp_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return OnArrowUp_assertThisInitialized(t); }
function OnArrowUp_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function OnArrowUp_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (OnArrowUp_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function OnArrowUp_getPrototypeOf(t) { return OnArrowUp_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, OnArrowUp_getPrototypeOf(t); }
function OnArrowUp_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && OnArrowUp_setPrototypeOf(t, e); }
function OnArrowUp_setPrototypeOf(t, e) { return OnArrowUp_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, OnArrowUp_setPrototypeOf(t, e); }



/**
 * OnArrowUp
 *
 * Event action handler class.
 */
var OnArrowUp_OnArrowUp = /*#__PURE__*/function (_EventAbstract) {
  function OnArrowUp() {
    OnArrowUp_classCallCheck(this, OnArrowUp);
    return OnArrowUp_callSuper(this, OnArrowUp, arguments);
  }
  OnArrowUp_inherits(OnArrowUp, _EventAbstract);
  return OnArrowUp_createClass(OnArrowUp, [{
    key: "exec",
    value:
    /**
     * Execute the action to the event.
     */
    function exec() {
      this.event.preventDefault();

      // Go to the previous item.
      var node = this.getElement('prev');
      if (node) {
        node.focus();
        return;
      }

      // Go to the prev item last subnav item.
      node = this.getElement('prevElementSiblingSubnavLast');
      if (node) {
        node.focus();
        return;
      }

      // Default to the end..
      var eventEnd = new OnEnd_OnEnd(this.item, this.event, this.target);
      eventEnd.init();
    }
  }]);
}(EventAbstract_EventAbstract);

;// ./src/js/components/secondary-nav/common/events/OnArrowLeft.js
function OnArrowLeft_typeof(o) { "@babel/helpers - typeof"; return OnArrowLeft_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, OnArrowLeft_typeof(o); }
function OnArrowLeft_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function OnArrowLeft_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, OnArrowLeft_toPropertyKey(o.key), o); } }
function OnArrowLeft_createClass(e, r, t) { return r && OnArrowLeft_defineProperties(e.prototype, r), t && OnArrowLeft_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function OnArrowLeft_toPropertyKey(t) { var i = OnArrowLeft_toPrimitive(t, "string"); return "symbol" == OnArrowLeft_typeof(i) ? i : i + ""; }
function OnArrowLeft_toPrimitive(t, r) { if ("object" != OnArrowLeft_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != OnArrowLeft_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function OnArrowLeft_callSuper(t, o, e) { return o = OnArrowLeft_getPrototypeOf(o), OnArrowLeft_possibleConstructorReturn(t, OnArrowLeft_isNativeReflectConstruct() ? Reflect.construct(o, e || [], OnArrowLeft_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function OnArrowLeft_possibleConstructorReturn(t, e) { if (e && ("object" == OnArrowLeft_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return OnArrowLeft_assertThisInitialized(t); }
function OnArrowLeft_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function OnArrowLeft_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (OnArrowLeft_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function OnArrowLeft_getPrototypeOf(t) { return OnArrowLeft_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, OnArrowLeft_getPrototypeOf(t); }
function OnArrowLeft_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && OnArrowLeft_setPrototypeOf(t, e); }
function OnArrowLeft_setPrototypeOf(t, e) { return OnArrowLeft_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, OnArrowLeft_setPrototypeOf(t, e); }



/**
 * OnArrowLeft
 *
 * Event action handler class.
 */
var events_OnArrowLeft_OnArrowLeft = /*#__PURE__*/function (_EventAbstract) {
  function OnArrowLeft() {
    OnArrowLeft_classCallCheck(this, OnArrowLeft);
    return OnArrowLeft_callSuper(this, OnArrowLeft, arguments);
  }
  OnArrowLeft_inherits(OnArrowLeft, _EventAbstract);
  return OnArrowLeft_createClass(OnArrowLeft, [{
    key: "exec",
    value:
    /**
     * Execute the action to the event.
     */
    function exec() {
      this.event.preventDefault();

      // If this is a nested item. Go back up a level.
      if (this.item.getDepth() > 1) {
        this.nestedLeft();
      }
      // Otherwise just to to the previous sibling.
      else if (this.item.getDepth() === 1) {
        this.firstLevelLeft();
      }
    }

    /**
     * Action to take on a first level left key press.
     */
  }, {
    key: "firstLevelLeft",
    value: function firstLevelLeft() {
      var upevent = new OnArrowUp_OnArrowUp(this.item, this.event, this.target);
      upevent.init();
    }

    /**
     * Action to take on a nested level left key press
     */
  }, {
    key: "nestedLeft",
    value: function nestedLeft() {
      var node = this.getElement('parentItem') || this.getElement('parentNavLast');
      // this.parentNav.closeSubNav();

      if (node) {
        node.focus();
      }
    }
  }]);
}(EventAbstract_EventAbstract);

;// ./src/js/components/secondary-nav/common/events/OnArrowRight.js
function OnArrowRight_typeof(o) { "@babel/helpers - typeof"; return OnArrowRight_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, OnArrowRight_typeof(o); }
function OnArrowRight_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function OnArrowRight_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, OnArrowRight_toPropertyKey(o.key), o); } }
function OnArrowRight_createClass(e, r, t) { return r && OnArrowRight_defineProperties(e.prototype, r), t && OnArrowRight_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function OnArrowRight_toPropertyKey(t) { var i = OnArrowRight_toPrimitive(t, "string"); return "symbol" == OnArrowRight_typeof(i) ? i : i + ""; }
function OnArrowRight_toPrimitive(t, r) { if ("object" != OnArrowRight_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != OnArrowRight_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function OnArrowRight_callSuper(t, o, e) { return o = OnArrowRight_getPrototypeOf(o), OnArrowRight_possibleConstructorReturn(t, OnArrowRight_isNativeReflectConstruct() ? Reflect.construct(o, e || [], OnArrowRight_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function OnArrowRight_possibleConstructorReturn(t, e) { if (e && ("object" == OnArrowRight_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return OnArrowRight_assertThisInitialized(t); }
function OnArrowRight_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function OnArrowRight_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (OnArrowRight_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function OnArrowRight_getPrototypeOf(t) { return OnArrowRight_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, OnArrowRight_getPrototypeOf(t); }
function OnArrowRight_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && OnArrowRight_setPrototypeOf(t, e); }
function OnArrowRight_setPrototypeOf(t, e) { return OnArrowRight_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, OnArrowRight_setPrototypeOf(t, e); }



/**
 * OnArrowRight
 *
 * Event action handler class.
 */
var common_events_OnArrowRight_OnArrowRight = /*#__PURE__*/function (_EventAbstract) {
  function OnArrowRight() {
    OnArrowRight_classCallCheck(this, OnArrowRight);
    return OnArrowRight_callSuper(this, OnArrowRight, arguments);
  }
  OnArrowRight_inherits(OnArrowRight, _EventAbstract);
  return OnArrowRight_createClass(OnArrowRight, [{
    key: "exec",
    value:
    /**
     * Execute the action to the event.
     */
    function exec() {
      // If we are in the second level or more we check about traversing
      // the parent.
      if (this.item.getDepth() > 1) {
        var node = this.getElement('parentNavNext');
        // this.parentNav.closeSubNav();

        if (node) {
          node.querySelector('a').focus();
        }
        // Go back to start.
        else {
          this.getElement('parentNavFirst').focus();
        }
      } else {
        var eventDown = new OnArrowDown_OnArrowDown(this.item, this.event, this.target);
        eventDown.init();
      }
    }
  }]);
}(EventAbstract_EventAbstract);

;// ./src/js/components/secondary-nav/common/events/OnEnter.js
function OnEnter_typeof(o) { "@babel/helpers - typeof"; return OnEnter_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, OnEnter_typeof(o); }
function OnEnter_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function OnEnter_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, OnEnter_toPropertyKey(o.key), o); } }
function OnEnter_createClass(e, r, t) { return r && OnEnter_defineProperties(e.prototype, r), t && OnEnter_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function OnEnter_toPropertyKey(t) { var i = OnEnter_toPrimitive(t, "string"); return "symbol" == OnEnter_typeof(i) ? i : i + ""; }
function OnEnter_toPrimitive(t, r) { if ("object" != OnEnter_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != OnEnter_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function OnEnter_callSuper(t, o, e) { return o = OnEnter_getPrototypeOf(o), OnEnter_possibleConstructorReturn(t, OnEnter_isNativeReflectConstruct() ? Reflect.construct(o, e || [], OnEnter_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function OnEnter_possibleConstructorReturn(t, e) { if (e && ("object" == OnEnter_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return OnEnter_assertThisInitialized(t); }
function OnEnter_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function OnEnter_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (OnEnter_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function OnEnter_getPrototypeOf(t) { return OnEnter_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, OnEnter_getPrototypeOf(t); }
function OnEnter_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && OnEnter_setPrototypeOf(t, e); }
function OnEnter_setPrototypeOf(t, e) { return OnEnter_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, OnEnter_setPrototypeOf(t, e); }


/**
 * OnEnter
 *
 * Event action handler class.
 */
var OnEnter_OnEnter = /*#__PURE__*/function (_EventAbstract) {
  function OnEnter() {
    OnEnter_classCallCheck(this, OnEnter);
    return OnEnter_callSuper(this, OnEnter, arguments);
  }
  OnEnter_inherits(OnEnter, _EventAbstract);
  return OnEnter_createClass(OnEnter, [{
    key: "exec",
    value:
    /**
     * Execute the action to the event.
     */
    function exec() {
      this.event.stopPropagation();
      this.event.preventDefault();
      window.location = this.target.getAttribute('href');
    }
  }]);
}(EventAbstract_EventAbstract);

;// ./src/js/components/secondary-nav/common/events/OnTab.js
function OnTab_typeof(o) { "@babel/helpers - typeof"; return OnTab_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, OnTab_typeof(o); }
function OnTab_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function OnTab_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, OnTab_toPropertyKey(o.key), o); } }
function OnTab_createClass(e, r, t) { return r && OnTab_defineProperties(e.prototype, r), t && OnTab_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function OnTab_toPropertyKey(t) { var i = OnTab_toPrimitive(t, "string"); return "symbol" == OnTab_typeof(i) ? i : i + ""; }
function OnTab_toPrimitive(t, r) { if ("object" != OnTab_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != OnTab_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function OnTab_callSuper(t, o, e) { return o = OnTab_getPrototypeOf(o), OnTab_possibleConstructorReturn(t, OnTab_isNativeReflectConstruct() ? Reflect.construct(o, e || [], OnTab_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function OnTab_possibleConstructorReturn(t, e) { if (e && ("object" == OnTab_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return OnTab_assertThisInitialized(t); }
function OnTab_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function OnTab_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (OnTab_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function OnTab_getPrototypeOf(t) { return OnTab_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, OnTab_getPrototypeOf(t); }
function OnTab_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && OnTab_setPrototypeOf(t, e); }
function OnTab_setPrototypeOf(t, e) { return OnTab_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, OnTab_setPrototypeOf(t, e); }


/**
 * OnTab
 *
 * Event action handler class.
 */
var events_OnTab_OnTab = /*#__PURE__*/function (_EventAbstract) {
  function OnTab() {
    OnTab_classCallCheck(this, OnTab);
    return OnTab_callSuper(this, OnTab, arguments);
  }
  OnTab_inherits(OnTab, _EventAbstract);
  return OnTab_createClass(OnTab, [{
    key: "exec",
    value:
    /**
     * Execute the action to the event.
     */
    function exec() {
      var shifted = event.shiftKey;
      var firstItem = false;
      var lastItem = false;
      try {
        firstItem = this.masterNav.elem.querySelector('a');
      } catch (err) {
        firstItem = this.masterNav.elem.firstElementChild;
      }
      try {
        lastItem = this.masterNav.elem.querySelector(':scope > ul > li:last-child');
      } catch (err) {
        lastItem = this.masterNav.elem.lastElementChild.lastElementChild;
      }

      // If shift key is held.
      if (shifted) {
        if (this.target === firstItem) {
          this.masterNav.closeAllSubNavs();
          return;
        }
      }
      // No shift key, just regular ol tab.
      else {
        if (this.target.parentNode === lastItem) {
          this.masterNav.closeAllSubNavs();
          return;
        }
      }
    }
  }]);
}(EventAbstract_EventAbstract);

;// ./src/js/components/secondary-nav/common/SecondaryNavItem.js
function SecondaryNavItem_typeof(o) { "@babel/helpers - typeof"; return SecondaryNavItem_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, SecondaryNavItem_typeof(o); }
function SecondaryNavItem_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function SecondaryNavItem_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, SecondaryNavItem_toPropertyKey(o.key), o); } }
function SecondaryNavItem_createClass(e, r, t) { return r && SecondaryNavItem_defineProperties(e.prototype, r), t && SecondaryNavItem_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function SecondaryNavItem_toPropertyKey(t) { var i = SecondaryNavItem_toPrimitive(t, "string"); return "symbol" == SecondaryNavItem_typeof(i) ? i : i + ""; }
function SecondaryNavItem_toPrimitive(t, r) { if ("object" != SecondaryNavItem_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != SecondaryNavItem_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }


// Keyboard control events.











/**
 * SecondaryNav Class
 */
var SecondaryNavItem_SecondaryNavItem = /*#__PURE__*/function () {
  /**
   * Initialize.
   *
   * @param {HTMLElement} element      The HTMLElement to bind to.
   * @param {Object|Mixed} masterNav   The top most navigation instance.
   * @param {Object|Mixed} parentNav   The parent nav instance if available.
   * @param {Object} options           An object of metadata.
   */
  function SecondaryNavItem(element, masterNav) {
    var parentNav = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
    var options = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};
    SecondaryNavItem_classCallCheck(this, SecondaryNavItem);
    this.elem = element;
    this.item = element.parentNode;
    this.masterNav = masterNav;
    this.parentNav = parentNav;
    this.depth = options.depth || 1;

    // Assign the event dispatcher and event registry.
    this.eventRegistry = this.createEventRegistry(options);
    this.dispatch = new EventHandlerDispatch_EventHandlerDispatch(element, this);
  }

  /**
   * Creates an event registry for handling types of events.
   *
   * This registry is used by the EventHandlerDispatch class to bind and
   * execute the events in the created property key.
   *
   * @param  {Object} options Options to merge in with the defaults.
   *
   * @return {Object} A key/value registry of events and handlers.
   */
  return SecondaryNavItem_createClass(SecondaryNavItem, [{
    key: "createEventRegistry",
    value: function createEventRegistry(options) {
      var registryDefaults = {
        onKeydownHome: OnHome_OnHome,
        onKeydownEnd: OnEnd_OnEnd,
        onKeydownTab: events_OnTab_OnTab,
        onKeydownSpace: events_OnSpace_OnSpace,
        onKeydownEnter: OnEnter_OnEnter,
        onKeydownEscape: OnEsc_OnEsc,
        onKeydownArrowUp: OnArrowUp_OnArrowUp,
        onKeydownArrowRight: common_events_OnArrowRight_OnArrowRight,
        onKeydownArrowDown: OnArrowDown_OnArrowDown,
        onKeydownArrowLeft: events_OnArrowLeft_OnArrowLeft
      };
      return Object.assign(registryDefaults, options.eventRegistry);
    }

    /**
     * Get the level of nesting for this nav.
     *
     * @return {Integer} The integer of depth starting at 1.
     */
  }, {
    key: "getDepth",
    value: function getDepth() {
      return this.depth;
    }
  }]);
}();

;// ./src/js/components/secondary-nav/static/events/OnArrowUpSubNav.js
function OnArrowUpSubNav_typeof(o) { "@babel/helpers - typeof"; return OnArrowUpSubNav_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, OnArrowUpSubNav_typeof(o); }
function OnArrowUpSubNav_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function OnArrowUpSubNav_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, OnArrowUpSubNav_toPropertyKey(o.key), o); } }
function OnArrowUpSubNav_createClass(e, r, t) { return r && OnArrowUpSubNav_defineProperties(e.prototype, r), t && OnArrowUpSubNav_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function OnArrowUpSubNav_toPropertyKey(t) { var i = OnArrowUpSubNav_toPrimitive(t, "string"); return "symbol" == OnArrowUpSubNav_typeof(i) ? i : i + ""; }
function OnArrowUpSubNav_toPrimitive(t, r) { if ("object" != OnArrowUpSubNav_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != OnArrowUpSubNav_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function OnArrowUpSubNav_callSuper(t, o, e) { return o = OnArrowUpSubNav_getPrototypeOf(o), OnArrowUpSubNav_possibleConstructorReturn(t, OnArrowUpSubNav_isNativeReflectConstruct() ? Reflect.construct(o, e || [], OnArrowUpSubNav_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function OnArrowUpSubNav_possibleConstructorReturn(t, e) { if (e && ("object" == OnArrowUpSubNav_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return OnArrowUpSubNav_assertThisInitialized(t); }
function OnArrowUpSubNav_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function OnArrowUpSubNav_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (OnArrowUpSubNav_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function OnArrowUpSubNav_getPrototypeOf(t) { return OnArrowUpSubNav_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, OnArrowUpSubNav_getPrototypeOf(t); }
function OnArrowUpSubNav_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && OnArrowUpSubNav_setPrototypeOf(t, e); }
function OnArrowUpSubNav_setPrototypeOf(t, e) { return OnArrowUpSubNav_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, OnArrowUpSubNav_setPrototypeOf(t, e); }



/**
 * OnArrowUpSubNav
 *
 * Event action handler class.
 */
var OnArrowUpSubNav = /*#__PURE__*/function (_EventAbstract) {
  function OnArrowUpSubNav() {
    OnArrowUpSubNav_classCallCheck(this, OnArrowUpSubNav);
    return OnArrowUpSubNav_callSuper(this, OnArrowUpSubNav, arguments);
  }
  OnArrowUpSubNav_inherits(OnArrowUpSubNav, _EventAbstract);
  return OnArrowUpSubNav_createClass(OnArrowUpSubNav, [{
    key: "exec",
    value:
    /**
     * Execute the action to the event.
     */
    function exec() {
      this.event.preventDefault();

      // Go to the previous item.
      var node = this.getElement('prevElement');
      if (node) {
        var links = node.querySelectorAll(':scope a');
        links[links.length - 1].focus();
        return;
      }

      // Go to the prev item last subnav item.
      node = this.getElement('parentItem');
      if (node) {
        node.focus();
        return;
      }

      // Default to the end..
      var eventEnd = new OnEnd_OnEnd(this.item, this.event, this.target);
      eventEnd.init();
    }
  }]);
}(EventAbstract_EventAbstract);

;// ./src/js/components/secondary-nav/static/events/OnArrowDownSubNav.js
function OnArrowDownSubNav_typeof(o) { "@babel/helpers - typeof"; return OnArrowDownSubNav_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, OnArrowDownSubNav_typeof(o); }
function OnArrowDownSubNav_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function OnArrowDownSubNav_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, OnArrowDownSubNav_toPropertyKey(o.key), o); } }
function OnArrowDownSubNav_createClass(e, r, t) { return r && OnArrowDownSubNav_defineProperties(e.prototype, r), t && OnArrowDownSubNav_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function OnArrowDownSubNav_toPropertyKey(t) { var i = OnArrowDownSubNav_toPrimitive(t, "string"); return "symbol" == OnArrowDownSubNav_typeof(i) ? i : i + ""; }
function OnArrowDownSubNav_toPrimitive(t, r) { if ("object" != OnArrowDownSubNav_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != OnArrowDownSubNav_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function OnArrowDownSubNav_callSuper(t, o, e) { return o = OnArrowDownSubNav_getPrototypeOf(o), OnArrowDownSubNav_possibleConstructorReturn(t, OnArrowDownSubNav_isNativeReflectConstruct() ? Reflect.construct(o, e || [], OnArrowDownSubNav_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function OnArrowDownSubNav_possibleConstructorReturn(t, e) { if (e && ("object" == OnArrowDownSubNav_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return OnArrowDownSubNav_assertThisInitialized(t); }
function OnArrowDownSubNav_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function OnArrowDownSubNav_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (OnArrowDownSubNav_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function OnArrowDownSubNav_getPrototypeOf(t) { return OnArrowDownSubNav_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, OnArrowDownSubNav_getPrototypeOf(t); }
function OnArrowDownSubNav_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && OnArrowDownSubNav_setPrototypeOf(t, e); }
function OnArrowDownSubNav_setPrototypeOf(t, e) { return OnArrowDownSubNav_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, OnArrowDownSubNav_setPrototypeOf(t, e); }


/**
 * OnArrowDownSubNav
 *
 * Event action handler class.
 */
var OnArrowDownSubNav = /*#__PURE__*/function (_EventAbstract) {
  function OnArrowDownSubNav() {
    OnArrowDownSubNav_classCallCheck(this, OnArrowDownSubNav);
    return OnArrowDownSubNav_callSuper(this, OnArrowDownSubNav, arguments);
  }
  OnArrowDownSubNav_inherits(OnArrowDownSubNav, _EventAbstract);
  return OnArrowDownSubNav_createClass(OnArrowDownSubNav, [{
    key: "exec",
    value:
    /**
     * Execute the action to the event.
     */
    function exec() {
      this.event.preventDefault();

      // Go to the first subnav item.
      var node = this.getElement('firstSubnavLink');
      if (node) {
        node.focus();
        return;
      }

      // Go to the next item.
      node = this.getElement('next');
      if (node) {
        node.focus();
        return;
      }

      // If there is no subnav item and no next item we must look for the next
      // link as far as it shows up in the html markup rather than the DOM. This
      // next link could be up and down a few sub nav items so the most reliable
      // key would be a tab key but as I am having trouble mimicking a tab event
      // with javascript we are going to do it the long way.

      var above = this.elem.parentNode;
      while (above.tagName !== 'NAV') {
        try {
          node = above.nextElementSibling.querySelector(':scope a:last-child');
        } catch (err) {
          // Nada.
        }

        // We got one or we are at the top.
        if (node) {
          break;
        }

        // Iterate.
        above = above.parentNode;
      }

      // If we find a link after all go to it.
      if (node) {
        node.focus();
        return;
      }

      // Go back to start, do not pass go.
      above.querySelector(':scope a').focus();
    }
  }]);
}(EventAbstract_EventAbstract);

;// ./src/js/components/secondary-nav/static/events/OnArrowRightSubNav.js
function OnArrowRightSubNav_typeof(o) { "@babel/helpers - typeof"; return OnArrowRightSubNav_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, OnArrowRightSubNav_typeof(o); }
function OnArrowRightSubNav_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function OnArrowRightSubNav_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, OnArrowRightSubNav_toPropertyKey(o.key), o); } }
function OnArrowRightSubNav_createClass(e, r, t) { return r && OnArrowRightSubNav_defineProperties(e.prototype, r), t && OnArrowRightSubNav_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function OnArrowRightSubNav_toPropertyKey(t) { var i = OnArrowRightSubNav_toPrimitive(t, "string"); return "symbol" == OnArrowRightSubNav_typeof(i) ? i : i + ""; }
function OnArrowRightSubNav_toPrimitive(t, r) { if ("object" != OnArrowRightSubNav_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != OnArrowRightSubNav_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function OnArrowRightSubNav_callSuper(t, o, e) { return o = OnArrowRightSubNav_getPrototypeOf(o), OnArrowRightSubNav_possibleConstructorReturn(t, OnArrowRightSubNav_isNativeReflectConstruct() ? Reflect.construct(o, e || [], OnArrowRightSubNav_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function OnArrowRightSubNav_possibleConstructorReturn(t, e) { if (e && ("object" == OnArrowRightSubNav_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return OnArrowRightSubNav_assertThisInitialized(t); }
function OnArrowRightSubNav_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function OnArrowRightSubNav_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (OnArrowRightSubNav_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function OnArrowRightSubNav_getPrototypeOf(t) { return OnArrowRightSubNav_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, OnArrowRightSubNav_getPrototypeOf(t); }
function OnArrowRightSubNav_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && OnArrowRightSubNav_setPrototypeOf(t, e); }
function OnArrowRightSubNav_setPrototypeOf(t, e) { return OnArrowRightSubNav_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, OnArrowRightSubNav_setPrototypeOf(t, e); }



/**
 * OnArrowRightSubNav
 *
 * Event action handler class.
 */
var OnArrowRightSubNav = /*#__PURE__*/function (_EventAbstract) {
  function OnArrowRightSubNav() {
    OnArrowRightSubNav_classCallCheck(this, OnArrowRightSubNav);
    return OnArrowRightSubNav_callSuper(this, OnArrowRightSubNav, arguments);
  }
  OnArrowRightSubNav_inherits(OnArrowRightSubNav, _EventAbstract);
  return OnArrowRightSubNav_createClass(OnArrowRightSubNav, [{
    key: "exec",
    value:
    /**
     * Execute the action to the event.
     */
    function exec() {
      // Go to the next sibling then go to the parent next sibling.
      var node = this.getElement('next');
      if (node) {
        node.focus();
        return;
      }

      // Do what down does.
      var eventDown = new OnArrowDownSubNav(this.item, this.event, this.target);
      eventDown.exec();
    }
  }]);
}(EventAbstract_EventAbstract);

;// ./src/js/components/secondary-nav/static/SecondarySubNavItemStatic.js
function SecondarySubNavItemStatic_typeof(o) { "@babel/helpers - typeof"; return SecondarySubNavItemStatic_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, SecondarySubNavItemStatic_typeof(o); }
function SecondarySubNavItemStatic_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function SecondarySubNavItemStatic_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, SecondarySubNavItemStatic_toPropertyKey(o.key), o); } }
function SecondarySubNavItemStatic_createClass(e, r, t) { return r && SecondarySubNavItemStatic_defineProperties(e.prototype, r), t && SecondarySubNavItemStatic_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function SecondarySubNavItemStatic_toPropertyKey(t) { var i = SecondarySubNavItemStatic_toPrimitive(t, "string"); return "symbol" == SecondarySubNavItemStatic_typeof(i) ? i : i + ""; }
function SecondarySubNavItemStatic_toPrimitive(t, r) { if ("object" != SecondarySubNavItemStatic_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != SecondarySubNavItemStatic_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function SecondarySubNavItemStatic_callSuper(t, o, e) { return o = SecondarySubNavItemStatic_getPrototypeOf(o), SecondarySubNavItemStatic_possibleConstructorReturn(t, SecondarySubNavItemStatic_isNativeReflectConstruct() ? Reflect.construct(o, e || [], SecondarySubNavItemStatic_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function SecondarySubNavItemStatic_possibleConstructorReturn(t, e) { if (e && ("object" == SecondarySubNavItemStatic_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return SecondarySubNavItemStatic_assertThisInitialized(t); }
function SecondarySubNavItemStatic_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function SecondarySubNavItemStatic_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (SecondarySubNavItemStatic_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = _get(SecondarySubNavItemStatic_getPrototypeOf(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
function _get() { return _get = "undefined" != typeof Reflect && Reflect.get ? Reflect.get.bind() : function (e, t, r) { var p = _superPropBase(e, t); if (p) { var n = Object.getOwnPropertyDescriptor(p, t); return n.get ? n.get.call(arguments.length < 3 ? e : r) : n.value; } }, _get.apply(null, arguments); }
function _superPropBase(t, o) { for (; !{}.hasOwnProperty.call(t, o) && null !== (t = SecondarySubNavItemStatic_getPrototypeOf(t));); return t; }
function SecondarySubNavItemStatic_getPrototypeOf(t) { return SecondarySubNavItemStatic_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, SecondarySubNavItemStatic_getPrototypeOf(t); }
function SecondarySubNavItemStatic_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && SecondarySubNavItemStatic_setPrototypeOf(t, e); }
function SecondarySubNavItemStatic_setPrototypeOf(t, e) { return SecondarySubNavItemStatic_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, SecondarySubNavItemStatic_setPrototypeOf(t, e); }





/**
 * SecondaryNav Class
 */
var SecondarySubNavItem = /*#__PURE__*/function (_SecondaryNavItem) {
  /**
   * Initialize.
   *
   * @param {HTMLElement} element      The HTMLElement to bind to.
   * @param {Object|Mixed} masterNav   The top most navigation instance.
   * @param {Object|Mixed} parentNav   The parent nav instance if available.
   * @param {Object} options           An object of metadata.
   */
  function SecondarySubNavItem(element, masterNav) {
    var parentNav = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
    var options = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};
    SecondarySubNavItemStatic_classCallCheck(this, SecondarySubNavItem);
    return SecondarySubNavItemStatic_callSuper(this, SecondarySubNavItem, [element, masterNav, parentNav, options]);
  }

  /**
   * Creates an event registry for handling types of events.
   *
   * This registry is used by the EventHandlerDispatch class to bind and
   * execute the events in the created property key.
   *
   * @param  {Object} options Options to merge in with the defaults.
   *
   * @return {Object} A key/value registry of events and handlers.
   */
  SecondarySubNavItemStatic_inherits(SecondarySubNavItem, _SecondaryNavItem);
  return SecondarySubNavItemStatic_createClass(SecondarySubNavItem, [{
    key: "createEventRegistry",
    value: function createEventRegistry(options) {
      var registryDefaults = _superPropGet(SecondarySubNavItem, "createEventRegistry", this, 3)([options]);
      registryDefaults['onKeydownArrowUp'] = OnArrowUpSubNav;
      registryDefaults['onKeydownArrowRight'] = OnArrowRightSubNav;
      registryDefaults['onKeydownArrowDown'] = OnArrowDownSubNav;
      return Object.assign(registryDefaults, options.eventRegistry);
    }

    /**
     * Close all SubNavs.
     *
     * Please override this method in your class.
     */
  }, {
    key: "closeAllSubNavs",
    value: function closeAllSubNavs() {
      return;
    }

    /**
     * Close this SubNav.
     *
     * Please override this method in your class.
     */
  }, {
    key: "closeSubNav",
    value: function closeSubNav() {
      return;
    }
  }]);
}(SecondaryNavItem_SecondaryNavItem);

;// ./src/js/components/secondary-nav/static/SecondaryNavStatic.js
function SecondaryNavStatic_typeof(o) { "@babel/helpers - typeof"; return SecondaryNavStatic_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, SecondaryNavStatic_typeof(o); }
function SecondaryNavStatic_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function SecondaryNavStatic_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, SecondaryNavStatic_toPropertyKey(o.key), o); } }
function SecondaryNavStatic_createClass(e, r, t) { return r && SecondaryNavStatic_defineProperties(e.prototype, r), t && SecondaryNavStatic_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function SecondaryNavStatic_toPropertyKey(t) { var i = SecondaryNavStatic_toPrimitive(t, "string"); return "symbol" == SecondaryNavStatic_typeof(i) ? i : i + ""; }
function SecondaryNavStatic_toPrimitive(t, r) { if ("object" != SecondaryNavStatic_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != SecondaryNavStatic_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function SecondaryNavStatic_callSuper(t, o, e) { return o = SecondaryNavStatic_getPrototypeOf(o), SecondaryNavStatic_possibleConstructorReturn(t, SecondaryNavStatic_isNativeReflectConstruct() ? Reflect.construct(o, e || [], SecondaryNavStatic_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function SecondaryNavStatic_possibleConstructorReturn(t, e) { if (e && ("object" == SecondaryNavStatic_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return SecondaryNavStatic_assertThisInitialized(t); }
function SecondaryNavStatic_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function SecondaryNavStatic_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (SecondaryNavStatic_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function SecondaryNavStatic_getPrototypeOf(t) { return SecondaryNavStatic_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, SecondaryNavStatic_getPrototypeOf(t); }
function SecondaryNavStatic_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && SecondaryNavStatic_setPrototypeOf(t, e); }
function SecondaryNavStatic_setPrototypeOf(t, e) { return SecondaryNavStatic_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, SecondaryNavStatic_setPrototypeOf(t, e); }







/**
 * A secondary menu with static links.
 */
var SecondaryNavStatic = /*#__PURE__*/function (_SecondaryNavAbstract) {
  /**
   * Initialize.
   *
   * @param {HTMLElement} elem  The outermost wrapper for the Navigation.
   * @param {Object} options    An object of metadata.
   */
  function SecondaryNavStatic(elem) {
    var _this;
    var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    SecondaryNavStatic_classCallCheck(this, SecondaryNavStatic);
    _this = SecondaryNavStatic_callSuper(this, SecondaryNavStatic, [elem, options]);
    _this.createSubNavItems();
    return _this;
  }

  /**
   * Function for creating a new nested navigation item.
   *
   * @param  {HTMLElement} item     The HTMLElement to attach a new subnav to.
   * @param  {Integer} depth        The level of nesting. (starts at 1)
   * @param  {Object|Mixed} parent  The parent subnav instance.
   *
   * @return {SecondarySubNavButtons} A brand new instance.
   */
  SecondaryNavStatic_inherits(SecondaryNavStatic, _SecondaryNavAbstract);
  return SecondaryNavStatic_createClass(SecondaryNavStatic, [{
    key: "newParentItem",
    value: function newParentItem(item, depth, parent) {
      var opts = Object.assign(this.options, {
        itemExpandedClass: this.options.itemExpandedClass,
        depth: depth
      });
      var nav = new SecondarySubNavItem(item, this, parent, opts);
      this.subNavItems.push(nav);
      return nav;
    }

    /**
     * Function for creating a new single tier navigation item.
     *
     * @param  {HTMLElement} item     The HTMLElement to attach a new subnav to.
     * @param  {Integer} depth        The level of nesting. (starts at 1)
     * @param  {Object|Mixed} parent  The parent subnav instance.
     *
     * @return {SecondaryNavItem} A brand new instance.
     */
  }, {
    key: "newNavItem",
    value: function newNavItem(item, depth, parent) {
      var myEvents = {
        onKeydownArrowDown: OnArrowDownSubNav,
        onKeydownArrowUp: OnArrowUpSubNav,
        onKeydownArrowRight: OnArrowRightSubNav
      };
      var opts = {
        eventRegistry: myEvents,
        depth: depth
      };
      var nav = new SecondaryNavItem_SecondaryNavItem(item, this, parent, opts);
      this.navItems.push(nav);
      return nav;
    }
  }]);
}(SecondaryNavAbstract_SecondaryNavAbstract);

;// ./src/js/components/secondary-nav/secondary-nav-static.js

document.addEventListener('DOMContentLoaded', function (event) {
  var secondaryNavs = document.querySelectorAll('.su-secondary-nav');
  secondaryNavs.forEach(function (nav, index) {
    if (nav.className.match(/su-secondary-nav--static/)) {
      var options = {
        expand: false,
        activeTrail: false
      };
      new SecondaryNavStatic(nav, options);
    }
  });
});
;// ./src/js/components/secondary-nav/index.js
// Get'm


// Not importing these as they come from Decanter already and we plan to remove
// them from this theme once the multi-menu has moved in to Decanter as well.
// import './secondary-nav-accordion.js';
// import './secondary-nav-buttons.js';
;// ./src/js/components/multi-menu/common/globals.js
// The css class that this following behaviour is applied to.
var multiMenuClass = 'su-multi-menu';

// All Secondary navs.
var multiMenus = document.querySelectorAll('.' + multiMenuClass);
;// ./src/js/components/secondary-nav/buttons/events/SubNavToggleClick.js
function SubNavToggleClick_typeof(o) { "@babel/helpers - typeof"; return SubNavToggleClick_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, SubNavToggleClick_typeof(o); }
function SubNavToggleClick_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function SubNavToggleClick_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, SubNavToggleClick_toPropertyKey(o.key), o); } }
function SubNavToggleClick_createClass(e, r, t) { return r && SubNavToggleClick_defineProperties(e.prototype, r), t && SubNavToggleClick_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function SubNavToggleClick_toPropertyKey(t) { var i = SubNavToggleClick_toPrimitive(t, "string"); return "symbol" == SubNavToggleClick_typeof(i) ? i : i + ""; }
function SubNavToggleClick_toPrimitive(t, r) { if ("object" != SubNavToggleClick_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != SubNavToggleClick_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function SubNavToggleClick_callSuper(t, o, e) { return o = SubNavToggleClick_getPrototypeOf(o), SubNavToggleClick_possibleConstructorReturn(t, SubNavToggleClick_isNativeReflectConstruct() ? Reflect.construct(o, e || [], SubNavToggleClick_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function SubNavToggleClick_possibleConstructorReturn(t, e) { if (e && ("object" == SubNavToggleClick_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return SubNavToggleClick_assertThisInitialized(t); }
function SubNavToggleClick_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function SubNavToggleClick_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (SubNavToggleClick_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function SubNavToggleClick_getPrototypeOf(t) { return SubNavToggleClick_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, SubNavToggleClick_getPrototypeOf(t); }
function SubNavToggleClick_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && SubNavToggleClick_setPrototypeOf(t, e); }
function SubNavToggleClick_setPrototypeOf(t, e) { return SubNavToggleClick_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, SubNavToggleClick_setPrototypeOf(t, e); }


/**
 * SubNavToggleClick
 *
 * Event action handler class.
 */
var SubNavToggleClick_SubNavToggleClick = /*#__PURE__*/function (_EventAbstract) {
  function SubNavToggleClick() {
    SubNavToggleClick_classCallCheck(this, SubNavToggleClick);
    return SubNavToggleClick_callSuper(this, SubNavToggleClick, arguments);
  }
  SubNavToggleClick_inherits(SubNavToggleClick, _EventAbstract);
  return SubNavToggleClick_createClass(SubNavToggleClick, [{
    key: "exec",
    value:
    /**
     * Execute the action to the event.
     */
    function exec() {
      if (this.parentNav.isExpanded()) {
        this.parentNav.closeSubNav();
        this.elem.blur();
        this.elem.focus();
      } else {
        this.parentNav.openSubNav();
      }
    }
  }]);
}(EventAbstract_EventAbstract);

;// ./src/js/components/secondary-nav/buttons/events/SubNavToggleSpace.js
function SubNavToggleSpace_typeof(o) { "@babel/helpers - typeof"; return SubNavToggleSpace_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, SubNavToggleSpace_typeof(o); }
function SubNavToggleSpace_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function SubNavToggleSpace_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, SubNavToggleSpace_toPropertyKey(o.key), o); } }
function SubNavToggleSpace_createClass(e, r, t) { return r && SubNavToggleSpace_defineProperties(e.prototype, r), t && SubNavToggleSpace_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function SubNavToggleSpace_toPropertyKey(t) { var i = SubNavToggleSpace_toPrimitive(t, "string"); return "symbol" == SubNavToggleSpace_typeof(i) ? i : i + ""; }
function SubNavToggleSpace_toPrimitive(t, r) { if ("object" != SubNavToggleSpace_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != SubNavToggleSpace_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function SubNavToggleSpace_callSuper(t, o, e) { return o = SubNavToggleSpace_getPrototypeOf(o), SubNavToggleSpace_possibleConstructorReturn(t, SubNavToggleSpace_isNativeReflectConstruct() ? Reflect.construct(o, e || [], SubNavToggleSpace_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function SubNavToggleSpace_possibleConstructorReturn(t, e) { if (e && ("object" == SubNavToggleSpace_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return SubNavToggleSpace_assertThisInitialized(t); }
function SubNavToggleSpace_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function SubNavToggleSpace_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (SubNavToggleSpace_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function SubNavToggleSpace_getPrototypeOf(t) { return SubNavToggleSpace_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, SubNavToggleSpace_getPrototypeOf(t); }
function SubNavToggleSpace_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && SubNavToggleSpace_setPrototypeOf(t, e); }
function SubNavToggleSpace_setPrototypeOf(t, e) { return SubNavToggleSpace_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, SubNavToggleSpace_setPrototypeOf(t, e); }



/**
 * SubNavToggleSpace
 *
 * Event action handler class.
 */
var SubNavToggleSpace_SubNavToggleSpace = /*#__PURE__*/function (_EventAbstract) {
  function SubNavToggleSpace() {
    SubNavToggleSpace_classCallCheck(this, SubNavToggleSpace);
    return SubNavToggleSpace_callSuper(this, SubNavToggleSpace, arguments);
  }
  SubNavToggleSpace_inherits(SubNavToggleSpace, _EventAbstract);
  return SubNavToggleSpace_createClass(SubNavToggleSpace, [{
    key: "exec",
    value:
    /**
     * Execute the action to the event.
     */
    function exec() {
      // No jumping around.
      this.event.preventDefault();

      // Call the click because it is pretty much the same thing.
      var eventClick = new SubNavToggleClick_SubNavToggleClick(this.item, this.event, this.target);
      eventClick.init();

      // Only focus on keyboard nav not on click.
      if (this.parentNav.isExpanded()) {
        var node = this.getElement('firstSubnavLink');
        if (node) {
          node.focus();
        }
      }
    }
  }]);
}(EventAbstract_EventAbstract);

;// ./src/js/components/secondary-nav/buttons/events/SubNavToggleArrowDown.js
function SubNavToggleArrowDown_typeof(o) { "@babel/helpers - typeof"; return SubNavToggleArrowDown_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, SubNavToggleArrowDown_typeof(o); }
function SubNavToggleArrowDown_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function SubNavToggleArrowDown_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, SubNavToggleArrowDown_toPropertyKey(o.key), o); } }
function SubNavToggleArrowDown_createClass(e, r, t) { return r && SubNavToggleArrowDown_defineProperties(e.prototype, r), t && SubNavToggleArrowDown_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function SubNavToggleArrowDown_toPropertyKey(t) { var i = SubNavToggleArrowDown_toPrimitive(t, "string"); return "symbol" == SubNavToggleArrowDown_typeof(i) ? i : i + ""; }
function SubNavToggleArrowDown_toPrimitive(t, r) { if ("object" != SubNavToggleArrowDown_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != SubNavToggleArrowDown_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function SubNavToggleArrowDown_callSuper(t, o, e) { return o = SubNavToggleArrowDown_getPrototypeOf(o), SubNavToggleArrowDown_possibleConstructorReturn(t, SubNavToggleArrowDown_isNativeReflectConstruct() ? Reflect.construct(o, e || [], SubNavToggleArrowDown_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function SubNavToggleArrowDown_possibleConstructorReturn(t, e) { if (e && ("object" == SubNavToggleArrowDown_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return SubNavToggleArrowDown_assertThisInitialized(t); }
function SubNavToggleArrowDown_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function SubNavToggleArrowDown_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (SubNavToggleArrowDown_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function SubNavToggleArrowDown_getPrototypeOf(t) { return SubNavToggleArrowDown_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, SubNavToggleArrowDown_getPrototypeOf(t); }
function SubNavToggleArrowDown_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && SubNavToggleArrowDown_setPrototypeOf(t, e); }
function SubNavToggleArrowDown_setPrototypeOf(t, e) { return SubNavToggleArrowDown_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, SubNavToggleArrowDown_setPrototypeOf(t, e); }


/**
 * SubNavToggleArrowDown
 *
 * Event action handler class.
 */
var SubNavToggleArrowDown_SubNavToggleArrowDown = /*#__PURE__*/function (_EventAbstract) {
  function SubNavToggleArrowDown() {
    SubNavToggleArrowDown_classCallCheck(this, SubNavToggleArrowDown);
    return SubNavToggleArrowDown_callSuper(this, SubNavToggleArrowDown, arguments);
  }
  SubNavToggleArrowDown_inherits(SubNavToggleArrowDown, _EventAbstract);
  return SubNavToggleArrowDown_createClass(SubNavToggleArrowDown, [{
    key: "exec",
    value:
    /**
     * Execute the action to the event.
     */
    function exec() {
      this.event.preventDefault();

      // If on the toggle item and the menu is expanded go down in to the first
      // menu item link as the focus.
      if (this.parentNav.isExpanded()) {
        event.stopPropagation();
        event.preventDefault();
        this.getElement('firstSubnavLink').focus();
      }
      // If current focus is on the toggle and the menu is not open, go to the
      // next sibling menu item.
      else {
        var node = this.getElement('next') || this.getElement('parentNavNext') || this.getElement('last');
        if (node) {
          node.focus();
        }
      }
    }
  }]);
}(EventAbstract_EventAbstract);

;// ./src/js/components/secondary-nav/buttons/events/SubNavToggleArrowLeft.js
function SubNavToggleArrowLeft_typeof(o) { "@babel/helpers - typeof"; return SubNavToggleArrowLeft_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, SubNavToggleArrowLeft_typeof(o); }
function SubNavToggleArrowLeft_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function SubNavToggleArrowLeft_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, SubNavToggleArrowLeft_toPropertyKey(o.key), o); } }
function SubNavToggleArrowLeft_createClass(e, r, t) { return r && SubNavToggleArrowLeft_defineProperties(e.prototype, r), t && SubNavToggleArrowLeft_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function SubNavToggleArrowLeft_toPropertyKey(t) { var i = SubNavToggleArrowLeft_toPrimitive(t, "string"); return "symbol" == SubNavToggleArrowLeft_typeof(i) ? i : i + ""; }
function SubNavToggleArrowLeft_toPrimitive(t, r) { if ("object" != SubNavToggleArrowLeft_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != SubNavToggleArrowLeft_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function SubNavToggleArrowLeft_callSuper(t, o, e) { return o = SubNavToggleArrowLeft_getPrototypeOf(o), SubNavToggleArrowLeft_possibleConstructorReturn(t, SubNavToggleArrowLeft_isNativeReflectConstruct() ? Reflect.construct(o, e || [], SubNavToggleArrowLeft_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function SubNavToggleArrowLeft_possibleConstructorReturn(t, e) { if (e && ("object" == SubNavToggleArrowLeft_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return SubNavToggleArrowLeft_assertThisInitialized(t); }
function SubNavToggleArrowLeft_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function SubNavToggleArrowLeft_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (SubNavToggleArrowLeft_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function SubNavToggleArrowLeft_getPrototypeOf(t) { return SubNavToggleArrowLeft_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, SubNavToggleArrowLeft_getPrototypeOf(t); }
function SubNavToggleArrowLeft_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && SubNavToggleArrowLeft_setPrototypeOf(t, e); }
function SubNavToggleArrowLeft_setPrototypeOf(t, e) { return SubNavToggleArrowLeft_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, SubNavToggleArrowLeft_setPrototypeOf(t, e); }


/**
 * SubNavToggleArrowLeft
 *
 * Event action handler class.
 */
var SubNavToggleArrowLeft_SubNavToggleArrowLeft = /*#__PURE__*/function (_EventAbstract) {
  function SubNavToggleArrowLeft() {
    SubNavToggleArrowLeft_classCallCheck(this, SubNavToggleArrowLeft);
    return SubNavToggleArrowLeft_callSuper(this, SubNavToggleArrowLeft, arguments);
  }
  SubNavToggleArrowLeft_inherits(SubNavToggleArrowLeft, _EventAbstract);
  return SubNavToggleArrowLeft_createClass(SubNavToggleArrowLeft, [{
    key: "exec",
    value:
    /**
     * Execute the action to the event.
     */
    function exec() {
      event.stopPropagation();
      event.preventDefault();
      this.parentNav.elem.focus();
    }
  }]);
}(EventAbstract_EventAbstract);

;// ./src/js/components/secondary-nav/buttons/events/SubNavToggleArrowUp.js
function SubNavToggleArrowUp_typeof(o) { "@babel/helpers - typeof"; return SubNavToggleArrowUp_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, SubNavToggleArrowUp_typeof(o); }
function SubNavToggleArrowUp_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function SubNavToggleArrowUp_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, SubNavToggleArrowUp_toPropertyKey(o.key), o); } }
function SubNavToggleArrowUp_createClass(e, r, t) { return r && SubNavToggleArrowUp_defineProperties(e.prototype, r), t && SubNavToggleArrowUp_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function SubNavToggleArrowUp_toPropertyKey(t) { var i = SubNavToggleArrowUp_toPrimitive(t, "string"); return "symbol" == SubNavToggleArrowUp_typeof(i) ? i : i + ""; }
function SubNavToggleArrowUp_toPrimitive(t, r) { if ("object" != SubNavToggleArrowUp_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != SubNavToggleArrowUp_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function SubNavToggleArrowUp_callSuper(t, o, e) { return o = SubNavToggleArrowUp_getPrototypeOf(o), SubNavToggleArrowUp_possibleConstructorReturn(t, SubNavToggleArrowUp_isNativeReflectConstruct() ? Reflect.construct(o, e || [], SubNavToggleArrowUp_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function SubNavToggleArrowUp_possibleConstructorReturn(t, e) { if (e && ("object" == SubNavToggleArrowUp_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return SubNavToggleArrowUp_assertThisInitialized(t); }
function SubNavToggleArrowUp_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function SubNavToggleArrowUp_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (SubNavToggleArrowUp_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function SubNavToggleArrowUp_getPrototypeOf(t) { return SubNavToggleArrowUp_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, SubNavToggleArrowUp_getPrototypeOf(t); }
function SubNavToggleArrowUp_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && SubNavToggleArrowUp_setPrototypeOf(t, e); }
function SubNavToggleArrowUp_setPrototypeOf(t, e) { return SubNavToggleArrowUp_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, SubNavToggleArrowUp_setPrototypeOf(t, e); }


/**
 * SubNavToggleArrowUp
 *
 * Event action handler class.
 */
var SubNavToggleArrowUp_SubNavToggleArrowUp = /*#__PURE__*/function (_EventAbstract) {
  function SubNavToggleArrowUp() {
    SubNavToggleArrowUp_classCallCheck(this, SubNavToggleArrowUp);
    return SubNavToggleArrowUp_callSuper(this, SubNavToggleArrowUp, arguments);
  }
  SubNavToggleArrowUp_inherits(SubNavToggleArrowUp, _EventAbstract);
  return SubNavToggleArrowUp_createClass(SubNavToggleArrowUp, [{
    key: "exec",
    value:
    /**
     * Execute the action to the event.
     */
    function exec() {
      this.event.preventDefault();

      // If the current focus is on the toggle and the menu is expanded, close
      // this nav menu and go to the parent list item.
      if (this.parentNav.isExpanded()) {
        event.stopPropagation();
        event.preventDefault();
        this.parentNav.closeSubNav();
        this.getElement('parentItem').focus();
      }
      // If the focus is on the toggle and the menu is not expanded, go to the
      // previous sibling item by calling the super method.
      else {
        var node = this.getElement('prev') || this.getElement('parentNavPrev') || this.getElement('first');
        if (node) {
          node.focus();
        }
      }
    }
  }]);
}(EventAbstract_EventAbstract);

;// ./src/js/components/secondary-nav/buttons/SubNavToggle.js
function SubNavToggle_typeof(o) { "@babel/helpers - typeof"; return SubNavToggle_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, SubNavToggle_typeof(o); }
function SubNavToggle_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function SubNavToggle_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, SubNavToggle_toPropertyKey(o.key), o); } }
function SubNavToggle_createClass(e, r, t) { return r && SubNavToggle_defineProperties(e.prototype, r), t && SubNavToggle_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function SubNavToggle_toPropertyKey(t) { var i = SubNavToggle_toPrimitive(t, "string"); return "symbol" == SubNavToggle_typeof(i) ? i : i + ""; }
function SubNavToggle_toPrimitive(t, r) { if ("object" != SubNavToggle_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != SubNavToggle_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }

// Events









/**
 * A stoggle button.
 */
var SubNavToggle_SubNavToggle = /*#__PURE__*/function () {
  /**
   * Initialize.
   *
   * @param {HTMLElement} element   The element to bind to.
   * @param {Object|Mixed} item     The parent nav instance.
   * @param {Object} options        Mixed meta information.
   */
  function SubNavToggle(element, item, options) {
    SubNavToggle_classCallCheck(this, SubNavToggle);
    this.parentNav = item;
    this.masterNav = item.masterNav;
    this.toggle = element;
    this.elem = element;
    this.options = options;

    // Assign the event dispatcher and event registry.
    this.eventRegistry = this.createEventRegistry(options);
    this.dispatch = new EventHandlerDispatch_EventHandlerDispatch(element, this);

    // Label the toggle to indicate which menu it opens
    this.elem.setAttribute('aria-label', 'Open the ' + this.parentNav.elem.innerText.trim() + ' menu');

    // Create an observer to watch if the toggled menu changes state.
    var self = this;
    this.observer = new MutationObserver(function () {
      var verb = 'Close';
      if (self.elem.getAttribute('aria-expanded') == 'false') {
        verb = 'Open';
      }
      self.elem.setAttribute('aria-label', verb + ' the ' + self.parentNav.elem.innerText.trim() + ' menu');
    });
    this.observer.observe(this.elem, {
      attributeFilter: ['aria-expanded']
    });
  }

  /**
   * Creates an event registry for handling types of events.
   *
   * This registry is used by the EventHandlerDispatch class to bind and
   * execute the events in the created property key.
   *
   * @param  {Object} options Options to merge in with the defaults.
   *
   * @return {Object} A key/value registry of events and handlers.
   */
  return SubNavToggle_createClass(SubNavToggle, [{
    key: "createEventRegistry",
    value: function createEventRegistry(options) {
      var registryDefaults = {
        onClick: SubNavToggleClick_SubNavToggleClick,
        onKeydownSpace: SubNavToggleSpace_SubNavToggleSpace,
        onKeydownEnter: SubNavToggleSpace_SubNavToggleSpace,
        onKeydownHome: OnHome_OnHome,
        onKeydownEnd: OnEnd_OnEnd,
        onKeydownEscape: OnEsc_OnEsc,
        onKeydownArrowUp: SubNavToggleArrowUp_SubNavToggleArrowUp,
        onKeydownArrowRight: SubNavToggleSpace_SubNavToggleSpace,
        onKeydownArrowDown: SubNavToggleArrowDown_SubNavToggleArrowDown,
        onKeydownArrowLeft: SubNavToggleArrowLeft_SubNavToggleArrowLeft
      };
      return Object.assign(registryDefaults, options.eventRegistry);
    }

    /**
     * Gets the current depth.
     * @return {Number} The depth of this nav item starting at 1.
     */
  }, {
    key: "getDepth",
    value: function getDepth() {
      return this.parentNav.getDepth();
    }
  }]);
}();

;// ./src/js/components/secondary-nav/buttons/events/OnArrowRight.js
function events_OnArrowRight_typeof(o) { "@babel/helpers - typeof"; return events_OnArrowRight_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, events_OnArrowRight_typeof(o); }
function events_OnArrowRight_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function events_OnArrowRight_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, events_OnArrowRight_toPropertyKey(o.key), o); } }
function events_OnArrowRight_createClass(e, r, t) { return r && events_OnArrowRight_defineProperties(e.prototype, r), t && events_OnArrowRight_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function events_OnArrowRight_toPropertyKey(t) { var i = events_OnArrowRight_toPrimitive(t, "string"); return "symbol" == events_OnArrowRight_typeof(i) ? i : i + ""; }
function events_OnArrowRight_toPrimitive(t, r) { if ("object" != events_OnArrowRight_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != events_OnArrowRight_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function events_OnArrowRight_callSuper(t, o, e) { return o = events_OnArrowRight_getPrototypeOf(o), events_OnArrowRight_possibleConstructorReturn(t, events_OnArrowRight_isNativeReflectConstruct() ? Reflect.construct(o, e || [], events_OnArrowRight_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function events_OnArrowRight_possibleConstructorReturn(t, e) { if (e && ("object" == events_OnArrowRight_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return events_OnArrowRight_assertThisInitialized(t); }
function events_OnArrowRight_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function events_OnArrowRight_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (events_OnArrowRight_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function events_OnArrowRight_getPrototypeOf(t) { return events_OnArrowRight_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, events_OnArrowRight_getPrototypeOf(t); }
function events_OnArrowRight_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && events_OnArrowRight_setPrototypeOf(t, e); }
function events_OnArrowRight_setPrototypeOf(t, e) { return events_OnArrowRight_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, events_OnArrowRight_setPrototypeOf(t, e); }


/**
 * OnArrowRight
 *
 * Event action handler class.
 */
var buttons_events_OnArrowRight_OnArrowRight = /*#__PURE__*/function (_EventAbstract) {
  function OnArrowRight() {
    events_OnArrowRight_classCallCheck(this, OnArrowRight);
    return events_OnArrowRight_callSuper(this, OnArrowRight, arguments);
  }
  events_OnArrowRight_inherits(OnArrowRight, _EventAbstract);
  return events_OnArrowRight_createClass(OnArrowRight, [{
    key: "exec",
    value:
    /**
     * Execute the action to the event.
     */
    function exec() {
      this.item.toggleElement.focus();
    }
  }]);
}(EventAbstract_EventAbstract);

;// ./src/js/components/secondary-nav/buttons/SecondarySubNavButtons.js
function SecondarySubNavButtons_typeof(o) { "@babel/helpers - typeof"; return SecondarySubNavButtons_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, SecondarySubNavButtons_typeof(o); }
function SecondarySubNavButtons_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function SecondarySubNavButtons_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, SecondarySubNavButtons_toPropertyKey(o.key), o); } }
function SecondarySubNavButtons_createClass(e, r, t) { return r && SecondarySubNavButtons_defineProperties(e.prototype, r), t && SecondarySubNavButtons_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function SecondarySubNavButtons_toPropertyKey(t) { var i = SecondarySubNavButtons_toPrimitive(t, "string"); return "symbol" == SecondarySubNavButtons_typeof(i) ? i : i + ""; }
function SecondarySubNavButtons_toPrimitive(t, r) { if ("object" != SecondarySubNavButtons_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != SecondarySubNavButtons_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }

// Events



// Keyboard events.









/**
 * SecondarySubNavButtons Class
 *
 * A sub menu class for creating a menu with toggle button functionality.
 */
var SecondarySubNavButtons_SecondarySubNavButtons = /*#__PURE__*/function () {
  /**
   * Initialize.
   *
   * @param {HTMLElement} element     The container wrapper for the nav.
   * @param {Object|Mixed} masterNav  The top most level navigation.
   * @param {Object|Mixed} parentNav  The parent navigation instance if this
   *                                  instance is nested.
   * @param {Object} options          A meta object of information and options.
   */
  function SecondarySubNavButtons(element, masterNav) {
    var parentNav = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
    var options = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};
    SecondarySubNavButtons_classCallCheck(this, SecondarySubNavButtons);
    // Vars.
    this.elem = element;
    this.item = element.parentNode;
    this.masterNav = masterNav;
    this.parentNav = parentNav;
    this.depth = options.depth || 1;
    this.preOpenSubnav = createEvent_createEvent('preOpenSubnav', {
      bubbles: true,
      data: this.item
    });
    this.postOpenSubnav = createEvent_createEvent('postOpenSubnav', {
      bubbles: true,
      data: this.item
    });
    this.preCloseSubnav = createEvent_createEvent('preCloseSubnav', {
      bubbles: true,
      data: this.item
    });
    this.postCloseSubnav = createEvent_createEvent('postCloseSubnav', {
      bubbles: true,
      data: this.item
    });

    // Merge in defaults.
    this.options = Object.assign({
      itemExpandedClass: 'su-secondary-nav__item--expanded',
      toggleClass: 'su-nav-toggle',
      toggleLabel: 'expand' + this.elem.innerText + ' menu',
      subNavToggleText: '+'
    }, options);

    // Assign the event dispatcher and event registry.
    this.eventRegistry = this.createEventRegistry(options);
    this.dispatch = new EventHandlerDispatch_EventHandlerDispatch(element, this);

    // Create the toggle buttons.
    this.initToggleButton(options);

    // Add the accessibility meta-information.
    this.initAccessibility();
  }

  /**
   * Initialize the toggle button.
   * @param {Object} options a meta object of information to pass along.
   */
  return SecondarySubNavButtons_createClass(SecondarySubNavButtons, [{
    key: "initToggleButton",
    value: function initToggleButton() {
      var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      this.toggleElement = this.createToggleButton();
      this.item.insertBefore(this.toggleElement, this.item.querySelector('ul'));
      this.toggle = new SubNavToggle_SubNavToggle(this.toggleElement, this, options);
    }

    /**
     * Creates an event registry for handling types of events.
     *
     * This registry is used by the EventHandlerDispatch class to bind and
     * execute the events in the created property key.
     *
     * @param  {Object} options Options to merge in with the defaults.
     *
     * @return {Object} A key/value registry of events and handlers.
     */
  }, {
    key: "createEventRegistry",
    value: function createEventRegistry(options) {
      var registryDefaults = {
        onKeydownSpace: events_OnSpace_OnSpace,
        onKeydownEnter: events_OnSpace_OnSpace,
        onKeydownHome: OnHome_OnHome,
        onKeydownEnd: OnEnd_OnEnd,
        onKeydownEscape: OnEsc_OnEsc,
        onKeydownArrowUp: OnArrowUp_OnArrowUp,
        onKeydownArrowRight: buttons_events_OnArrowRight_OnArrowRight,
        onKeydownArrowDown: OnArrowDown_OnArrowDown,
        onKeydownArrowLeft: events_OnArrowLeft_OnArrowLeft
      };
      return Object.assign(registryDefaults, options.eventRegistry);
    }

    /**
     * Create and a button for the expand/collapse actions.
     *
     * @return {HTMLElement} The button toggle.
     */
  }, {
    key: "createToggleButton",
    value: function createToggleButton() {
      var element = document.createElement('button');
      var label = document.createTextNode(this.options.subNavToggleText);

      // Give this instance a unique ID.
      var id = 'toggle-' + Math.random().toString(36).substr(2, 9);
      element.setAttribute('class', this.options.toggleClass);
      element.setAttribute('aria-expanded', 'false');
      element.setAttribute('aria-label', this.options.toggleLabel);
      element.setAttribute('id', id);
      element.appendChild(label);
      return element;
    }

    /**
     * Is this expanded? Can only return TRUE if this is a subnav trigger.
     *
     * @return {Boolean}
     *  Wether or not the item is expanded.
     */
  }, {
    key: "isExpanded",
    value: function isExpanded() {
      return this.toggleElement.getAttribute('aria-expanded') === 'true';
    }

    /**
     * Handles the opening of a sub-nav.
     *
     * If this is a subnav trigger, open the corresponding subnav.
     * Optionally force focus on the first element in the subnav
     * (for keyboard nav).
     */
  }, {
    key: "openSubNav",
    value: function openSubNav() {
      this.elem.dispatchEvent(this.preOpenSubnav);
      this.toggleElement.setAttribute('aria-expanded', true);
      this.item.classList.add(this.options.itemExpandedClass);
      this.elem.dispatchEvent(this.postOpenSubnav);
    }

    /**
     * Handles the closing of a subnav.
     *
     * If this is a subnav trigger or an item in a subnav, close the
     * corresponding subnav. Optionally force focus on the trigger.
     */
  }, {
    key: "closeSubNav",
    value: function closeSubNav() {
      this.elem.dispatchEvent(this.preCloseSubnav);
      this.toggleElement.setAttribute('aria-expanded', false);
      this.item.classList.remove(this.options.itemExpandedClass);
      this.elem.dispatchEvent(this.postCloseSubnav);
    }

    /**
     * Get the level of nesting for this nav.
     *
     * @return {Integer} The integer of depth starting at 1.
     */
  }, {
    key: "getDepth",
    value: function getDepth() {
      return this.depth;
    }

    /**
     * Adds ids, labels, and other meta-information.
     */
  }, {
    key: "initAccessibility",
    value: function initAccessibility() {
      var elementIndex = Array.from(this.item.parentNode.children).indexOf(this.item);
      var elemID = this.toggleElement.getAttribute('id');
      var section = this.item.querySelector(':scope > ul');
      var sectionID = section.getAttribute('id');

      // If there isnt an ID on the element add one.
      if (!elemID) {
        elemID = 'su-acc-' + this.getDepth() + '-' + elementIndex;
        this.toggleElement.setAttribute('id', elemID);
      }
      if (!sectionID) {
        sectionID = 'su-acs-' + this.getDepth() + '-' + elementIndex;
        var uniqueIndex = 0;
        // Make sure the id attribute will be unique.
        while (document.getElementById(sectionID)) {
          sectionID = 'su-acs-' + this.getDepth() + '-' + elementIndex + '-' + uniqueIndex;
          uniqueIndex++;
        }
        // If there isnt an ID on the section add one.
        section.setAttribute('id', sectionID);
      }

      // Add the aria stuff.
      section.setAttribute('aria-labelledby', elemID);
    }
  }]);
}();

;// ./src/js/components/secondary-nav/buttons/SecondaryNavButtons.js
function SecondaryNavButtons_typeof(o) { "@babel/helpers - typeof"; return SecondaryNavButtons_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, SecondaryNavButtons_typeof(o); }
function SecondaryNavButtons_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function SecondaryNavButtons_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, SecondaryNavButtons_toPropertyKey(o.key), o); } }
function SecondaryNavButtons_createClass(e, r, t) { return r && SecondaryNavButtons_defineProperties(e.prototype, r), t && SecondaryNavButtons_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function SecondaryNavButtons_toPropertyKey(t) { var i = SecondaryNavButtons_toPrimitive(t, "string"); return "symbol" == SecondaryNavButtons_typeof(i) ? i : i + ""; }
function SecondaryNavButtons_toPrimitive(t, r) { if ("object" != SecondaryNavButtons_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != SecondaryNavButtons_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function SecondaryNavButtons_callSuper(t, o, e) { return o = SecondaryNavButtons_getPrototypeOf(o), SecondaryNavButtons_possibleConstructorReturn(t, SecondaryNavButtons_isNativeReflectConstruct() ? Reflect.construct(o, e || [], SecondaryNavButtons_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function SecondaryNavButtons_possibleConstructorReturn(t, e) { if (e && ("object" == SecondaryNavButtons_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return SecondaryNavButtons_assertThisInitialized(t); }
function SecondaryNavButtons_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function SecondaryNavButtons_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (SecondaryNavButtons_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function SecondaryNavButtons_getPrototypeOf(t) { return SecondaryNavButtons_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, SecondaryNavButtons_getPrototypeOf(t); }
function SecondaryNavButtons_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && SecondaryNavButtons_setPrototypeOf(t, e); }
function SecondaryNavButtons_setPrototypeOf(t, e) { return SecondaryNavButtons_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, SecondaryNavButtons_setPrototypeOf(t, e); }




/**
 * A secondary menu with toggle buttons.
 */
var SecondaryNavButtons_SecondaryNavButtons = /*#__PURE__*/function (_SecondaryNavAbstract) {
  /**
   * Initialize.
   *
   * @param {HTMLElement} elem  The outermost wrapper for the Navigation.
   * @param {Object} options    An object of metadata.
   */
  function SecondaryNavButtons(elem) {
    var _this;
    var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    SecondaryNavButtons_classCallCheck(this, SecondaryNavButtons);
    // Merge with the default options.
    options = Object.assign({
      itemExpandedClass: 'su-secondary-nav__item--expanded',
      toggleClass: 'su-nav-toggle',
      toggleLabel: 'expand menu',
      subNavToggleText: ''
    }, options);

    // Call the super.
    _this = SecondaryNavButtons_callSuper(this, SecondaryNavButtons, [elem, options]);

    // Ok do the creation.
    _this.createSubNavItems();

    // Add additional eventListeners.
    _this.addEventListeners();

    // Expand the path.
    // Expand the active path.
    if (_this.options.expand) {
      _this.activePath.expandActivePath();
    }
    return _this;
  }

  /**
   * Add the additional state handling after the abstract option has run.
   *
   * @param  {HTMLElement} item The HTMLElement being acted upon.
   */
  SecondaryNavButtons_inherits(SecondaryNavButtons, _SecondaryNavAbstract);
  return SecondaryNavButtons_createClass(SecondaryNavButtons, [{
    key: "expandActivePathItem",
    value: function expandActivePathItem(item) {
      var node = item.querySelector('.' + this.options.toggleClass);
      if (node) {
        node.setAttribute('aria-expanded', 'true');
      }
    }

    /**
     * Function for creating a new nested navigation item.
     *
     * @param  {HTMLElement} item     The HTMLElement to attach a new subnav to.
     * @param  {Integer} depth        The level of nesting. (starts at 1)
     * @param  {Object|Mixed} parent  The parent subnav instance.
     *
     * @return {SecondarySubNavButtons} A brand new instance.
     */
  }, {
    key: "newParentItem",
    value: function newParentItem(item, depth, parent) {
      var opts = Object.assign(this.options, {
        itemExpandedClass: this.options.itemExpandedClass,
        depth: depth
      });
      var nav = new SecondarySubNavButtons_SecondarySubNavButtons(item, this, parent, opts);
      this.subNavItems.push(nav);
      return nav;
    }

    /**
     * Function for creating a new single tier navigation item.
     *
     * @param  {HTMLElement} item     The HTMLElement to attach a new subnav to.
     * @param  {Integer} depth        The level of nesting. (starts at 1)
     * @param  {Object|Mixed} parent  The parent subnav instance.
     *
     * @return {SecondaryNavItem} A brand new instance.
     */
  }, {
    key: "newNavItem",
    value: function newNavItem(item, depth, parent) {
      var nav = new SecondaryNavItem_SecondaryNavItem(item, this, parent, {
        depth: depth
      });
      this.navItems.push(nav);
      return nav;
    }

    /**
     * Adds additional event listeners.
     */
  }, {
    key: "addEventListeners",
    value: function addEventListeners() {
      var _this2 = this;
      // Clicking anywhere outside of attached nav closes all the children.
      document.addEventListener('preOpenSubnav', function (event) {
        _this2.subNavItems.forEach(function (subnav, index) {
          if (SecondaryNavButtons_typeof(subnav.item) == undefined) {
            return;
          }
          if (subnav.item.contains(event.target)) {
            return;
          }
          subnav.closeSubNav();
        });
      });
    }
  }]);
}(SecondaryNavAbstract_SecondaryNavAbstract);

;// ./src/js/components/multi-menu/buttons/events/MultiMenuEventAbstract.js
function MultiMenuEventAbstract_typeof(o) { "@babel/helpers - typeof"; return MultiMenuEventAbstract_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, MultiMenuEventAbstract_typeof(o); }
function MultiMenuEventAbstract_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function MultiMenuEventAbstract_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, MultiMenuEventAbstract_toPropertyKey(o.key), o); } }
function MultiMenuEventAbstract_createClass(e, r, t) { return r && MultiMenuEventAbstract_defineProperties(e.prototype, r), t && MultiMenuEventAbstract_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function MultiMenuEventAbstract_toPropertyKey(t) { var i = MultiMenuEventAbstract_toPrimitive(t, "string"); return "symbol" == MultiMenuEventAbstract_typeof(i) ? i : i + ""; }
function MultiMenuEventAbstract_toPrimitive(t, r) { if ("object" != MultiMenuEventAbstract_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != MultiMenuEventAbstract_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function MultiMenuEventAbstract_callSuper(t, o, e) { return o = MultiMenuEventAbstract_getPrototypeOf(o), MultiMenuEventAbstract_possibleConstructorReturn(t, MultiMenuEventAbstract_isNativeReflectConstruct() ? Reflect.construct(o, e || [], MultiMenuEventAbstract_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function MultiMenuEventAbstract_possibleConstructorReturn(t, e) { if (e && ("object" == MultiMenuEventAbstract_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return MultiMenuEventAbstract_assertThisInitialized(t); }
function MultiMenuEventAbstract_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function MultiMenuEventAbstract_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (MultiMenuEventAbstract_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function MultiMenuEventAbstract_getPrototypeOf(t) { return MultiMenuEventAbstract_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, MultiMenuEventAbstract_getPrototypeOf(t); }
function MultiMenuEventAbstract_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && MultiMenuEventAbstract_setPrototypeOf(t, e); }
function MultiMenuEventAbstract_setPrototypeOf(t, e) { return MultiMenuEventAbstract_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, MultiMenuEventAbstract_setPrototypeOf(t, e); }


/**
 * MultiMenuEventAbstract class
 *
 * Event action handler with common code for MultiMenu.
 */
var MultiMenuEventAbstract = /*#__PURE__*/function (_EventAbstract) {
  function MultiMenuEventAbstract() {
    MultiMenuEventAbstract_classCallCheck(this, MultiMenuEventAbstract);
    return MultiMenuEventAbstract_callSuper(this, MultiMenuEventAbstract, arguments);
  }
  MultiMenuEventAbstract_inherits(MultiMenuEventAbstract, _EventAbstract);
  return MultiMenuEventAbstract_createClass(MultiMenuEventAbstract, [{
    key: "exec",
    value:
    /**
     * Execute the action to the event.
     */
    function exec() {
      if (this.isDesktop()) {
        this.handleDesktop();
      } else {
        this.handleMobile();
      }
    }

    /**
     * Handle the events for desktop sized screens.
     */
  }, {
    key: "handleDesktop",
    value: function handleDesktop() {
      // Do something.
    }

    /**
     * Handle the events for mobile sized screens.
     */
  }, {
    key: "handleMobile",
    value: function handleMobile() {
      // Do something.
    }
  }]);
}(EventAbstract_EventAbstract);

;// ./src/js/components/multi-menu/buttons/events/OnArrowRightToggleLV1.js
function OnArrowRightToggleLV1_typeof(o) { "@babel/helpers - typeof"; return OnArrowRightToggleLV1_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, OnArrowRightToggleLV1_typeof(o); }
function OnArrowRightToggleLV1_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function OnArrowRightToggleLV1_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, OnArrowRightToggleLV1_toPropertyKey(o.key), o); } }
function OnArrowRightToggleLV1_createClass(e, r, t) { return r && OnArrowRightToggleLV1_defineProperties(e.prototype, r), t && OnArrowRightToggleLV1_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function OnArrowRightToggleLV1_toPropertyKey(t) { var i = OnArrowRightToggleLV1_toPrimitive(t, "string"); return "symbol" == OnArrowRightToggleLV1_typeof(i) ? i : i + ""; }
function OnArrowRightToggleLV1_toPrimitive(t, r) { if ("object" != OnArrowRightToggleLV1_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != OnArrowRightToggleLV1_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function OnArrowRightToggleLV1_callSuper(t, o, e) { return o = OnArrowRightToggleLV1_getPrototypeOf(o), OnArrowRightToggleLV1_possibleConstructorReturn(t, OnArrowRightToggleLV1_isNativeReflectConstruct() ? Reflect.construct(o, e || [], OnArrowRightToggleLV1_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function OnArrowRightToggleLV1_possibleConstructorReturn(t, e) { if (e && ("object" == OnArrowRightToggleLV1_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return OnArrowRightToggleLV1_assertThisInitialized(t); }
function OnArrowRightToggleLV1_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function OnArrowRightToggleLV1_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (OnArrowRightToggleLV1_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function OnArrowRightToggleLV1_getPrototypeOf(t) { return OnArrowRightToggleLV1_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, OnArrowRightToggleLV1_getPrototypeOf(t); }
function OnArrowRightToggleLV1_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && OnArrowRightToggleLV1_setPrototypeOf(t, e); }
function OnArrowRightToggleLV1_setPrototypeOf(t, e) { return OnArrowRightToggleLV1_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, OnArrowRightToggleLV1_setPrototypeOf(t, e); }



/**
 * OnArrowLeft
 *
 * Event action handler class.
 */
var OnArrowRightToggleLV1 = /*#__PURE__*/function (_MultiMenuEventAbstra) {
  function OnArrowRightToggleLV1() {
    OnArrowRightToggleLV1_classCallCheck(this, OnArrowRightToggleLV1);
    return OnArrowRightToggleLV1_callSuper(this, OnArrowRightToggleLV1, arguments);
  }
  OnArrowRightToggleLV1_inherits(OnArrowRightToggleLV1, _MultiMenuEventAbstra);
  return OnArrowRightToggleLV1_createClass(OnArrowRightToggleLV1, [{
    key: "handleDesktop",
    value:
    /**
     * Handle the events for desktop sized screens.
     */
    function handleDesktop() {
      if (this.getElement('next')) {
        this.getElement('next').focus();
      } else {
        this.getElement('parentNavFirst').focus();
      }
    }

    /**
     * Handle the events for mobile sized screens.
     */
  }, {
    key: "handleMobile",
    value: function handleMobile() {
      var expandEvent = new SubNavToggleSpace_SubNavToggleSpace(this.item, this.event, this.target);
      expandEvent.init();
    }
  }]);
}(MultiMenuEventAbstract);

;// ./src/js/components/multi-menu/buttons/events/OnArrowLeftLV1.js
function OnArrowLeftLV1_typeof(o) { "@babel/helpers - typeof"; return OnArrowLeftLV1_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, OnArrowLeftLV1_typeof(o); }
function OnArrowLeftLV1_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function OnArrowLeftLV1_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, OnArrowLeftLV1_toPropertyKey(o.key), o); } }
function OnArrowLeftLV1_createClass(e, r, t) { return r && OnArrowLeftLV1_defineProperties(e.prototype, r), t && OnArrowLeftLV1_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function OnArrowLeftLV1_toPropertyKey(t) { var i = OnArrowLeftLV1_toPrimitive(t, "string"); return "symbol" == OnArrowLeftLV1_typeof(i) ? i : i + ""; }
function OnArrowLeftLV1_toPrimitive(t, r) { if ("object" != OnArrowLeftLV1_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != OnArrowLeftLV1_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function OnArrowLeftLV1_callSuper(t, o, e) { return o = OnArrowLeftLV1_getPrototypeOf(o), OnArrowLeftLV1_possibleConstructorReturn(t, OnArrowLeftLV1_isNativeReflectConstruct() ? Reflect.construct(o, e || [], OnArrowLeftLV1_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function OnArrowLeftLV1_possibleConstructorReturn(t, e) { if (e && ("object" == OnArrowLeftLV1_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return OnArrowLeftLV1_assertThisInitialized(t); }
function OnArrowLeftLV1_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function OnArrowLeftLV1_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (OnArrowLeftLV1_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function OnArrowLeftLV1_getPrototypeOf(t) { return OnArrowLeftLV1_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, OnArrowLeftLV1_getPrototypeOf(t); }
function OnArrowLeftLV1_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && OnArrowLeftLV1_setPrototypeOf(t, e); }
function OnArrowLeftLV1_setPrototypeOf(t, e) { return OnArrowLeftLV1_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, OnArrowLeftLV1_setPrototypeOf(t, e); }



/**
 * OnArrowLeft
 *
 * Event action handler class.
 */
var OnArrowLeftLV1 = /*#__PURE__*/function (_MultiMenuEventAbstra) {
  function OnArrowLeftLV1() {
    OnArrowLeftLV1_classCallCheck(this, OnArrowLeftLV1);
    return OnArrowLeftLV1_callSuper(this, OnArrowLeftLV1, arguments);
  }
  OnArrowLeftLV1_inherits(OnArrowLeftLV1, _MultiMenuEventAbstra);
  return OnArrowLeftLV1_createClass(OnArrowLeftLV1, [{
    key: "handleDesktop",
    value:
    /**
     * Handle the events for desktop sized screens.
     */
    function handleDesktop() {
      if (drupalSettings.stanford_basic.nav_dropdown_enabled) {
        this.handleMobile();
        return;
      }
      var element = this.getElement('prev') || this.getElement('last');
      element.focus();
    }

    /**
     * Handle the events for mobile sized screens.
     */
  }, {
    key: "handleMobile",
    value: function handleMobile() {
      var classicEvent = new events_OnArrowLeft_OnArrowLeft(this.item, this.event, this.target);
      classicEvent.init();
    }
  }]);
}(MultiMenuEventAbstract);

;// ./src/js/components/multi-menu/buttons/events/OnArrowRightLV1.js
function OnArrowRightLV1_typeof(o) { "@babel/helpers - typeof"; return OnArrowRightLV1_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, OnArrowRightLV1_typeof(o); }
function OnArrowRightLV1_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function OnArrowRightLV1_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, OnArrowRightLV1_toPropertyKey(o.key), o); } }
function OnArrowRightLV1_createClass(e, r, t) { return r && OnArrowRightLV1_defineProperties(e.prototype, r), t && OnArrowRightLV1_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function OnArrowRightLV1_toPropertyKey(t) { var i = OnArrowRightLV1_toPrimitive(t, "string"); return "symbol" == OnArrowRightLV1_typeof(i) ? i : i + ""; }
function OnArrowRightLV1_toPrimitive(t, r) { if ("object" != OnArrowRightLV1_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != OnArrowRightLV1_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function OnArrowRightLV1_callSuper(t, o, e) { return o = OnArrowRightLV1_getPrototypeOf(o), OnArrowRightLV1_possibleConstructorReturn(t, OnArrowRightLV1_isNativeReflectConstruct() ? Reflect.construct(o, e || [], OnArrowRightLV1_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function OnArrowRightLV1_possibleConstructorReturn(t, e) { if (e && ("object" == OnArrowRightLV1_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return OnArrowRightLV1_assertThisInitialized(t); }
function OnArrowRightLV1_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function OnArrowRightLV1_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (OnArrowRightLV1_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function OnArrowRightLV1_getPrototypeOf(t) { return OnArrowRightLV1_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, OnArrowRightLV1_getPrototypeOf(t); }
function OnArrowRightLV1_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && OnArrowRightLV1_setPrototypeOf(t, e); }
function OnArrowRightLV1_setPrototypeOf(t, e) { return OnArrowRightLV1_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, OnArrowRightLV1_setPrototypeOf(t, e); }



/**
 * OnArrowLeft
 *
 * Event action handler class.
 */
var OnArrowRightLV1 = /*#__PURE__*/function (_MultiMenuEventAbstra) {
  function OnArrowRightLV1() {
    OnArrowRightLV1_classCallCheck(this, OnArrowRightLV1);
    return OnArrowRightLV1_callSuper(this, OnArrowRightLV1, arguments);
  }
  OnArrowRightLV1_inherits(OnArrowRightLV1, _MultiMenuEventAbstra);
  return OnArrowRightLV1_createClass(OnArrowRightLV1, [{
    key: "handleDesktop",
    value:
    /**
     * Handle the events for desktop sized screens.
     */
    function handleDesktop() {
      if (drupalSettings.stanford_basic.nav_dropdown_enabled) {
        this.handleMobile();
        return;
      }
      var element = this.getElement('next') || this.getElement('first');
      element.focus();
    }

    /**
     * Handle the events for mobile sized screens.
     */
  }, {
    key: "handleMobile",
    value: function handleMobile() {
      var node = this.elem.nextElementSibling;
      if (node) {
        node.focus();
        return;
      }
      var classicEvent = new common_events_OnArrowRight_OnArrowRight(this.item, this.event, this.target);
      classicEvent.init();
    }
  }]);
}(MultiMenuEventAbstract);

;// ./src/js/components/multi-menu/buttons/events/OnArrowDownToggleLV1.js
function OnArrowDownToggleLV1_typeof(o) { "@babel/helpers - typeof"; return OnArrowDownToggleLV1_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, OnArrowDownToggleLV1_typeof(o); }
function OnArrowDownToggleLV1_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function OnArrowDownToggleLV1_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, OnArrowDownToggleLV1_toPropertyKey(o.key), o); } }
function OnArrowDownToggleLV1_createClass(e, r, t) { return r && OnArrowDownToggleLV1_defineProperties(e.prototype, r), t && OnArrowDownToggleLV1_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function OnArrowDownToggleLV1_toPropertyKey(t) { var i = OnArrowDownToggleLV1_toPrimitive(t, "string"); return "symbol" == OnArrowDownToggleLV1_typeof(i) ? i : i + ""; }
function OnArrowDownToggleLV1_toPrimitive(t, r) { if ("object" != OnArrowDownToggleLV1_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != OnArrowDownToggleLV1_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function OnArrowDownToggleLV1_callSuper(t, o, e) { return o = OnArrowDownToggleLV1_getPrototypeOf(o), OnArrowDownToggleLV1_possibleConstructorReturn(t, OnArrowDownToggleLV1_isNativeReflectConstruct() ? Reflect.construct(o, e || [], OnArrowDownToggleLV1_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function OnArrowDownToggleLV1_possibleConstructorReturn(t, e) { if (e && ("object" == OnArrowDownToggleLV1_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return OnArrowDownToggleLV1_assertThisInitialized(t); }
function OnArrowDownToggleLV1_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function OnArrowDownToggleLV1_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (OnArrowDownToggleLV1_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function OnArrowDownToggleLV1_getPrototypeOf(t) { return OnArrowDownToggleLV1_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, OnArrowDownToggleLV1_getPrototypeOf(t); }
function OnArrowDownToggleLV1_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && OnArrowDownToggleLV1_setPrototypeOf(t, e); }
function OnArrowDownToggleLV1_setPrototypeOf(t, e) { return OnArrowDownToggleLV1_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, OnArrowDownToggleLV1_setPrototypeOf(t, e); }



/**
 * OnArrowLeft
 *
 * Event action handler class.
 */
var OnArrowDownToggleLV1 = /*#__PURE__*/function (_MultiMenuEventAbstra) {
  function OnArrowDownToggleLV1() {
    OnArrowDownToggleLV1_classCallCheck(this, OnArrowDownToggleLV1);
    return OnArrowDownToggleLV1_callSuper(this, OnArrowDownToggleLV1, arguments);
  }
  OnArrowDownToggleLV1_inherits(OnArrowDownToggleLV1, _MultiMenuEventAbstra);
  return OnArrowDownToggleLV1_createClass(OnArrowDownToggleLV1, [{
    key: "handleDesktop",
    value:
    /**
     * Handle the events for desktop sized screens.
     */
    function handleDesktop() {
      if (drupalSettings.stanford_basic.nav_dropdown_enabled) {
        this.handleMobile();
        return;
      }
    }

    /**
     * Handle the events for mobile sized screens.
     */
  }, {
    key: "handleMobile",
    value: function handleMobile() {
      var expandEvent = new SubNavToggleSpace_SubNavToggleSpace(this.item, this.event, this.target);
      expandEvent.init();
    }
  }]);
}(MultiMenuEventAbstract);

;// ./src/js/components/multi-menu/buttons/events/OnClickToggleLV1.js
function OnClickToggleLV1_typeof(o) { "@babel/helpers - typeof"; return OnClickToggleLV1_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, OnClickToggleLV1_typeof(o); }
function OnClickToggleLV1_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function OnClickToggleLV1_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, OnClickToggleLV1_toPropertyKey(o.key), o); } }
function OnClickToggleLV1_createClass(e, r, t) { return r && OnClickToggleLV1_defineProperties(e.prototype, r), t && OnClickToggleLV1_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function OnClickToggleLV1_toPropertyKey(t) { var i = OnClickToggleLV1_toPrimitive(t, "string"); return "symbol" == OnClickToggleLV1_typeof(i) ? i : i + ""; }
function OnClickToggleLV1_toPrimitive(t, r) { if ("object" != OnClickToggleLV1_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != OnClickToggleLV1_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function OnClickToggleLV1_callSuper(t, o, e) { return o = OnClickToggleLV1_getPrototypeOf(o), OnClickToggleLV1_possibleConstructorReturn(t, OnClickToggleLV1_isNativeReflectConstruct() ? Reflect.construct(o, e || [], OnClickToggleLV1_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function OnClickToggleLV1_possibleConstructorReturn(t, e) { if (e && ("object" == OnClickToggleLV1_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return OnClickToggleLV1_assertThisInitialized(t); }
function OnClickToggleLV1_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function OnClickToggleLV1_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (OnClickToggleLV1_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function OnClickToggleLV1_getPrototypeOf(t) { return OnClickToggleLV1_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, OnClickToggleLV1_getPrototypeOf(t); }
function OnClickToggleLV1_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && OnClickToggleLV1_setPrototypeOf(t, e); }
function OnClickToggleLV1_setPrototypeOf(t, e) { return OnClickToggleLV1_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, OnClickToggleLV1_setPrototypeOf(t, e); }



/**
 * OnArrowLeft
 *
 * Event action handler class.
 */
var OnClickToggleLV1 = /*#__PURE__*/function (_MultiMenuEventAbstra) {
  function OnClickToggleLV1() {
    OnClickToggleLV1_classCallCheck(this, OnClickToggleLV1);
    return OnClickToggleLV1_callSuper(this, OnClickToggleLV1, arguments);
  }
  OnClickToggleLV1_inherits(OnClickToggleLV1, _MultiMenuEventAbstra);
  return OnClickToggleLV1_createClass(OnClickToggleLV1, [{
    key: "handleDesktop",
    value:
    /**
     * Handle the events for desktop sized screens.
     */
    function handleDesktop() {
      if (drupalSettings.stanford_basic.nav_dropdown_enabled) {
        this.handleMobile();
        return;
      }
      this.event.preventDefault();
      var node = this.parentNav.elem;
      node.click();
      node.focus();
    }

    /**
     * Handle the events for mobile sized screens.
     */
  }, {
    key: "handleMobile",
    value: function handleMobile() {
      var clickEvent = new SubNavToggleClick_SubNavToggleClick(this.item, this.event, this.target);
      clickEvent.init();
    }
  }]);
}(MultiMenuEventAbstract);

;// ./src/js/components/multi-menu/buttons/events/OnArrowUpToggleLV1.js
function OnArrowUpToggleLV1_typeof(o) { "@babel/helpers - typeof"; return OnArrowUpToggleLV1_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, OnArrowUpToggleLV1_typeof(o); }
function OnArrowUpToggleLV1_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function OnArrowUpToggleLV1_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, OnArrowUpToggleLV1_toPropertyKey(o.key), o); } }
function OnArrowUpToggleLV1_createClass(e, r, t) { return r && OnArrowUpToggleLV1_defineProperties(e.prototype, r), t && OnArrowUpToggleLV1_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function OnArrowUpToggleLV1_toPropertyKey(t) { var i = OnArrowUpToggleLV1_toPrimitive(t, "string"); return "symbol" == OnArrowUpToggleLV1_typeof(i) ? i : i + ""; }
function OnArrowUpToggleLV1_toPrimitive(t, r) { if ("object" != OnArrowUpToggleLV1_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != OnArrowUpToggleLV1_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function OnArrowUpToggleLV1_callSuper(t, o, e) { return o = OnArrowUpToggleLV1_getPrototypeOf(o), OnArrowUpToggleLV1_possibleConstructorReturn(t, OnArrowUpToggleLV1_isNativeReflectConstruct() ? Reflect.construct(o, e || [], OnArrowUpToggleLV1_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function OnArrowUpToggleLV1_possibleConstructorReturn(t, e) { if (e && ("object" == OnArrowUpToggleLV1_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return OnArrowUpToggleLV1_assertThisInitialized(t); }
function OnArrowUpToggleLV1_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function OnArrowUpToggleLV1_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (OnArrowUpToggleLV1_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function OnArrowUpToggleLV1_getPrototypeOf(t) { return OnArrowUpToggleLV1_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, OnArrowUpToggleLV1_getPrototypeOf(t); }
function OnArrowUpToggleLV1_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && OnArrowUpToggleLV1_setPrototypeOf(t, e); }
function OnArrowUpToggleLV1_setPrototypeOf(t, e) { return OnArrowUpToggleLV1_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, OnArrowUpToggleLV1_setPrototypeOf(t, e); }



/**
 * OnArrowLeft
 *
 * Event action handler class.
 */
var OnArrowUpToggleLV1 = /*#__PURE__*/function (_MultiMenuEventAbstra) {
  function OnArrowUpToggleLV1() {
    OnArrowUpToggleLV1_classCallCheck(this, OnArrowUpToggleLV1);
    return OnArrowUpToggleLV1_callSuper(this, OnArrowUpToggleLV1, arguments);
  }
  OnArrowUpToggleLV1_inherits(OnArrowUpToggleLV1, _MultiMenuEventAbstra);
  return OnArrowUpToggleLV1_createClass(OnArrowUpToggleLV1, [{
    key: "handleDesktop",
    value:
    /**
     * Handle the events for desktop sized screens.
     */
    function handleDesktop() {
      if (drupalSettings.stanford_basic.nav_dropdown_enabled) {
        this.handleMobile();
        return;
      }
    }

    /**
     * Handle the events for mobile sized screens.
     */
  }, {
    key: "handleMobile",
    value: function handleMobile() {
      var collapseEvent = new SubNavToggleSpace_SubNavToggleSpace(this.item, this.event, this.target);
      collapseEvent.init();
    }
  }]);
}(MultiMenuEventAbstract);

;// ./src/js/components/multi-menu/buttons/MultiSubNavButtons.js
function MultiSubNavButtons_typeof(o) { "@babel/helpers - typeof"; return MultiSubNavButtons_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, MultiSubNavButtons_typeof(o); }
function MultiSubNavButtons_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function MultiSubNavButtons_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, MultiSubNavButtons_toPropertyKey(o.key), o); } }
function MultiSubNavButtons_createClass(e, r, t) { return r && MultiSubNavButtons_defineProperties(e.prototype, r), t && MultiSubNavButtons_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function MultiSubNavButtons_toPropertyKey(t) { var i = MultiSubNavButtons_toPrimitive(t, "string"); return "symbol" == MultiSubNavButtons_typeof(i) ? i : i + ""; }
function MultiSubNavButtons_toPrimitive(t, r) { if ("object" != MultiSubNavButtons_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != MultiSubNavButtons_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function MultiSubNavButtons_callSuper(t, o, e) { return o = MultiSubNavButtons_getPrototypeOf(o), MultiSubNavButtons_possibleConstructorReturn(t, MultiSubNavButtons_isNativeReflectConstruct() ? Reflect.construct(o, e || [], MultiSubNavButtons_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function MultiSubNavButtons_possibleConstructorReturn(t, e) { if (e && ("object" == MultiSubNavButtons_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return MultiSubNavButtons_assertThisInitialized(t); }
function MultiSubNavButtons_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function MultiSubNavButtons_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (MultiSubNavButtons_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function MultiSubNavButtons_superPropGet(t, o, e, r) { var p = MultiSubNavButtons_get(MultiSubNavButtons_getPrototypeOf(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
function MultiSubNavButtons_get() { return MultiSubNavButtons_get = "undefined" != typeof Reflect && Reflect.get ? Reflect.get.bind() : function (e, t, r) { var p = MultiSubNavButtons_superPropBase(e, t); if (p) { var n = Object.getOwnPropertyDescriptor(p, t); return n.get ? n.get.call(arguments.length < 3 ? e : r) : n.value; } }, MultiSubNavButtons_get.apply(null, arguments); }
function MultiSubNavButtons_superPropBase(t, o) { for (; !{}.hasOwnProperty.call(t, o) && null !== (t = MultiSubNavButtons_getPrototypeOf(t));); return t; }
function MultiSubNavButtons_getPrototypeOf(t) { return MultiSubNavButtons_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, MultiSubNavButtons_getPrototypeOf(t); }
function MultiSubNavButtons_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && MultiSubNavButtons_setPrototypeOf(t, e); }
function MultiSubNavButtons_setPrototypeOf(t, e) { return MultiSubNavButtons_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, MultiSubNavButtons_setPrototypeOf(t, e); }









/**
 * SecondarySubNavAccordion Class
 *
 * A sub menu class for creating a menu with accordion functionality.
 */
var MultiSubNavButtons = /*#__PURE__*/function (_SecondarySubNavButto) {
  function MultiSubNavButtons() {
    MultiSubNavButtons_classCallCheck(this, MultiSubNavButtons);
    return MultiSubNavButtons_callSuper(this, MultiSubNavButtons, arguments);
  }
  MultiSubNavButtons_inherits(MultiSubNavButtons, _SecondarySubNavButto);
  return MultiSubNavButtons_createClass(MultiSubNavButtons, [{
    key: "createEventRegistry",
    value:
    /**
     * Creates an event registry for handling types of events.
     *
     * This registry is used by the EventHandlerDispatch class to bind and
     * execute the events in the created property key.
     *
     * @param  {Object} options Options to merge in with the defaults.
     *
     * @return {Object} A key/value registry of events and handlers.
     */
    function createEventRegistry(options) {
      var registryDefaults = MultiSubNavButtons_superPropGet(MultiSubNavButtons, "createEventRegistry", this, 3)([{}]);
      // If we are the first level (top) we need to adjust for mobile vs desktop.
      if (this.getDepth() === 1) {
        registryDefaults = Object.assign(registryDefaults, {
          onKeydownArrowLeft: OnArrowLeftLV1,
          onKeydownArrowRight: OnArrowRightLV1
        });
      }
      return registryDefaults;
    }

    /**
     * Initialize the toggle button.
     */
  }, {
    key: "initToggleButton",
    value: function initToggleButton() {
      var options = {};
      // Overrides for level 1 desktop.
      if (this.getDepth() === 1) {
        options.eventRegistry = {
          onKeydownArrowRight: OnArrowRightToggleLV1,
          onKeydownArrowDown: OnArrowDownToggleLV1,
          onKeydownArrowUp: OnArrowUpToggleLV1,
          onClick: OnClickToggleLV1,
          onKeydownEscape: OnEsc_OnEsc
        };
      }

      // Do eet.
      MultiSubNavButtons_superPropGet(MultiSubNavButtons, "initToggleButton", this, 3)([options]);
    }
  }]);
}(SecondarySubNavButtons_SecondarySubNavButtons);

;// ./src/js/components/multi-menu/buttons/MultiNavItem.js
function MultiNavItem_typeof(o) { "@babel/helpers - typeof"; return MultiNavItem_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, MultiNavItem_typeof(o); }
function MultiNavItem_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function MultiNavItem_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, MultiNavItem_toPropertyKey(o.key), o); } }
function MultiNavItem_createClass(e, r, t) { return r && MultiNavItem_defineProperties(e.prototype, r), t && MultiNavItem_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function MultiNavItem_toPropertyKey(t) { var i = MultiNavItem_toPrimitive(t, "string"); return "symbol" == MultiNavItem_typeof(i) ? i : i + ""; }
function MultiNavItem_toPrimitive(t, r) { if ("object" != MultiNavItem_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != MultiNavItem_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function MultiNavItem_callSuper(t, o, e) { return o = MultiNavItem_getPrototypeOf(o), MultiNavItem_possibleConstructorReturn(t, MultiNavItem_isNativeReflectConstruct() ? Reflect.construct(o, e || [], MultiNavItem_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function MultiNavItem_possibleConstructorReturn(t, e) { if (e && ("object" == MultiNavItem_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return MultiNavItem_assertThisInitialized(t); }
function MultiNavItem_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function MultiNavItem_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (MultiNavItem_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function MultiNavItem_superPropGet(t, o, e, r) { var p = MultiNavItem_get(MultiNavItem_getPrototypeOf(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
function MultiNavItem_get() { return MultiNavItem_get = "undefined" != typeof Reflect && Reflect.get ? Reflect.get.bind() : function (e, t, r) { var p = MultiNavItem_superPropBase(e, t); if (p) { var n = Object.getOwnPropertyDescriptor(p, t); return n.get ? n.get.call(arguments.length < 3 ? e : r) : n.value; } }, MultiNavItem_get.apply(null, arguments); }
function MultiNavItem_superPropBase(t, o) { for (; !{}.hasOwnProperty.call(t, o) && null !== (t = MultiNavItem_getPrototypeOf(t));); return t; }
function MultiNavItem_getPrototypeOf(t) { return MultiNavItem_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, MultiNavItem_getPrototypeOf(t); }
function MultiNavItem_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && MultiNavItem_setPrototypeOf(t, e); }
function MultiNavItem_setPrototypeOf(t, e) { return MultiNavItem_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, MultiNavItem_setPrototypeOf(t, e); }




/**
 * SecondarySubNavAccordion Class
 *
 * A sub menu class for creating a menu with accordion functionality.
 */
var MultiNavItem = /*#__PURE__*/function (_SecondaryNavItem) {
  function MultiNavItem() {
    MultiNavItem_classCallCheck(this, MultiNavItem);
    return MultiNavItem_callSuper(this, MultiNavItem, arguments);
  }
  MultiNavItem_inherits(MultiNavItem, _SecondaryNavItem);
  return MultiNavItem_createClass(MultiNavItem, [{
    key: "createEventRegistry",
    value:
    /**
     * Creates an event registry for handling types of events.
     *
     * This registry is used by the EventHandlerDispatch class to bind and
     * execute the events in the created property key.
     *
     * @param  {Object} options Options to merge in with the defaults.
     *
     * @return {Object} A key/value registry of events and handlers.
     */
    function createEventRegistry(options) {
      var registryDefaults = MultiNavItem_superPropGet(MultiNavItem, "createEventRegistry", this, 3)([{}]);
      if (this.getDepth() === 1) {
        registryDefaults = Object.assign(registryDefaults, {
          onKeydownArrowLeft: OnArrowLeftLV1,
          onKeydownArrowRight: OnArrowRightLV1
        });
      }
      return registryDefaults;
    }
  }]);
}(SecondaryNavItem_SecondaryNavItem);

;// ./src/js/components/multi-menu/buttons/MultiMenuButtons.js
function MultiMenuButtons_typeof(o) { "@babel/helpers - typeof"; return MultiMenuButtons_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, MultiMenuButtons_typeof(o); }
function MultiMenuButtons_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function MultiMenuButtons_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, MultiMenuButtons_toPropertyKey(o.key), o); } }
function MultiMenuButtons_createClass(e, r, t) { return r && MultiMenuButtons_defineProperties(e.prototype, r), t && MultiMenuButtons_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function MultiMenuButtons_toPropertyKey(t) { var i = MultiMenuButtons_toPrimitive(t, "string"); return "symbol" == MultiMenuButtons_typeof(i) ? i : i + ""; }
function MultiMenuButtons_toPrimitive(t, r) { if ("object" != MultiMenuButtons_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != MultiMenuButtons_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function MultiMenuButtons_callSuper(t, o, e) { return o = MultiMenuButtons_getPrototypeOf(o), MultiMenuButtons_possibleConstructorReturn(t, MultiMenuButtons_isNativeReflectConstruct() ? Reflect.construct(o, e || [], MultiMenuButtons_getPrototypeOf(t).constructor) : o.apply(t, e)); }
function MultiMenuButtons_possibleConstructorReturn(t, e) { if (e && ("object" == MultiMenuButtons_typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return MultiMenuButtons_assertThisInitialized(t); }
function MultiMenuButtons_assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function MultiMenuButtons_isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (MultiMenuButtons_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function MultiMenuButtons_getPrototypeOf(t) { return MultiMenuButtons_getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, MultiMenuButtons_getPrototypeOf(t); }
function MultiMenuButtons_inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && MultiMenuButtons_setPrototypeOf(t, e); }
function MultiMenuButtons_setPrototypeOf(t, e) { return MultiMenuButtons_setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, MultiMenuButtons_setPrototypeOf(t, e); }




/**
 * A secondary menu with toggle buttons.
 */
var MultiMenuButtons = /*#__PURE__*/function (_SecondaryNavButtons) {
  /**
   * Initialize.
   *
   * @param {HTMLElement} elem  The outermost wrapper for the Navigation.
   * @param {Object} options    An object of metadata.
   */
  function MultiMenuButtons(elem) {
    var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    MultiMenuButtons_classCallCheck(this, MultiMenuButtons);
    // Set some default options.
    var defaultOptions = {
      itemClass: 'su-multi-menu__item',
      itemExpandedClass: 'su-multi-menu__item--expanded',
      itemActiveClass: 'su-multi-menu__item--current',
      itemActiveTrailClass: 'su-multi-menu__item--active-trail',
      itemParentClass: 'su-multi-menu__item--parent',
      expand: false,
      activeTrail: false
    };

    // Merge in defaults.
    options = Object.assign(defaultOptions, options);

    // Kick it.
    return MultiMenuButtons_callSuper(this, MultiMenuButtons, [elem, options]);
  }

  /**
   * Function for creating a new nested navigation item.
   *
   * @param  {HTMLElement} item     The HTMLElement to attach a new subnav to.
   * @param  {Integer} depth        The level of nesting. (starts at 1)
   * @param  {Object|Mixed} parent  The parent subnav instance.
   *
   * @return {SecondarySubNavAccordion} A brand new instance.
   */
  MultiMenuButtons_inherits(MultiMenuButtons, _SecondaryNavButtons);
  return MultiMenuButtons_createClass(MultiMenuButtons, [{
    key: "newParentItem",
    value: function newParentItem(item, depth, parent) {
      var opts = Object.assign(this.options, {
        depth: depth
      });
      var nav = new MultiSubNavButtons(item, this, parent, opts);
      this.subNavItems.push(nav);
      return nav;
    }

    /**
     * Function for creating a new nested navigation item.
     *
     * @param  {HTMLElement} item     The HTMLElement to attach a new subnav to.
     * @param  {Integer} depth        The level of nesting. (starts at 1)
     * @param  {Object|Mixed} parent  The parent subnav instance.
     *
     * @return {SecondarySubNavAccordion} A brand new instance.
     */
  }, {
    key: "newNavItem",
    value: function newNavItem(item, depth, parent) {
      var opts = Object.assign(this.options, {
        depth: depth
      });
      var nav = new MultiNavItem(item, this, parent, opts);
      this.navItems.push(nav);
      return nav;
    }
  }]);
}(SecondaryNavButtons_SecondaryNavButtons);

;// ./src/js/components/multi-menu/common/MobileToggle.js
function MobileToggle_typeof(o) { "@babel/helpers - typeof"; return MobileToggle_typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, MobileToggle_typeof(o); }
function MobileToggle_classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function MobileToggle_defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, MobileToggle_toPropertyKey(o.key), o); } }
function MobileToggle_createClass(e, r, t) { return r && MobileToggle_defineProperties(e.prototype, r), t && MobileToggle_defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function MobileToggle_toPropertyKey(t) { var i = MobileToggle_toPrimitive(t, "string"); return "symbol" == MobileToggle_typeof(i) ? i : i + ""; }
function MobileToggle_toPrimitive(t, r) { if ("object" != MobileToggle_typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != MobileToggle_typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }



/**
 * Nav Toggle for the mobile/desktop button. Opens and closes the navigation
 */
var MobileToggle = /*#__PURE__*/function () {
  /**
   * Create a new toggle.
   *
   * @param {HTMLLIElement} element  - The <li> that is the NavItem in the DOM.
   * @param {*|Object} nav           - The main nav object that this toggle controls.
   * @param {Object} options         - A simple object of key values used as
   *                                   configuration options for each instance.
   */
  function MobileToggle(element, nav, options) {
    var _this = this;
    MobileToggle_classCallCheck(this, MobileToggle);
    // Params.
    this.elem = element;
    this.nav = nav;

    // Merge options with defaults.
    this.options = Object.assign({
      toggleText: element.innerText || 'Open',
      closeText: 'Close',
      firstLevelSelector: ':scope > .su-multi-menu__menu'
    }, options);
    this.openEvent = createEvent_createEvent('openNav');
    this.closeEvent = createEvent_createEvent('closeNav');
    this.firstLevel = this.nav.elem.querySelector(this.options.firstLevelSelector);

    // Event listeners.
    this.elem.addEventListener('click', this);
    this.elem.addEventListener('keydown', this);

    // Hide mobile menu by default.
    this.closeNav();

    // Clicking anywhere outside of attached nav closes all the children.
    document.addEventListener('click', function (event) {
      _this.outOfBounds(event);
    });
    document.addEventListener('keyup', function (event) {
      _this.outOfBounds(event);
    });
    document.addEventListener('closeAllMobileNavs', function (event) {
      _this.closeNav();
      if (_this.nav.elem.contains(event.target)) {
        _this.elem.focus();
      }
    });
  }

  /**
   * Handler for all events attached to an instance of this class. This method
   * must exist when events are bound to an instance of a class
   * (vs a function). This method is called for all events bound to an
   * instance. It inspects the instance (this) for an appropriate handler
   * based on the event type. If found, it dispatches the event to the
   * appropriate handler.
   *
   * @param {KeyboardEvent} event - The keyboard event object.
   *
   * @return {*}
   *  Whatever the dispatched handler returns (in our case nothing)
   */
  return MobileToggle_createClass(MobileToggle, [{
    key: "handleEvent",
    value: function handleEvent(event) {
      event = event || window.event;
      // If this class has an onEvent method, e.g. onClick, onKeydown,
      // invoke it.
      var handler = 'on' + event.type.charAt(0).toUpperCase() + event.type.slice(1);
      if (typeof this[handler] === 'function') {
        // The element that was clicked.
        var target = event.target || event.srcElement;
        return this[handler](event, target);
      }
    }

    /**
     * Handle the click event on the toggle.
     *
     * @param {Event} event         - The event object.
     * @param {HTMLElement} target  - The HTML element target.
     */
  }, {
    key: "onClick",
    value: function onClick(event, target) {
      // Only act if the target is my element.
      if (target !== this.elem) {
        return;
      }

      // Don't go nowhere.
      event.preventDefault();

      // Toggle open and close.
      if (this.isExpanded()) {
        this.closeNav();
      } else {
        this.openNav();
      }
    }

    /**
     * Event handler for key: Down Arrow.
     *
     * @param {KeyboardEvent} event - The keyboard event object.
     * @param {HTMLElement} target  - The HTML element target.
     */
  }, {
    key: "onKeydown",
    value: function onKeydown(event, target) {
      var theKey = event.key || event.keyCode;

      // Do the click toggle for enter and space keys.
      if (keyboard_isEnter(theKey) || keyboard_isSpace(theKey)) {
        this.onClick(event, this.elem);
        if (this.isExpanded()) {
          this.nav.elem.querySelector('a').focus();
        }
      }
    }

    /**
     * Checks to see if an event happened outside of the navigation context.
     *
     * @param  {*|KeyboardEvent|MouseEvent} event Some sort of event.
     */
  }, {
    key: "outOfBounds",
    value: function outOfBounds(event) {
      // The element that was clicked.
      var target = event.target || event.srcElement;
      // If the clicked element was not in my nav wrapper, close me.
      var found = target.closest('#' + this.nav.id);
      if (!found) {
        this.closeNav();
        this.nav.closeAllSubNavs();
      }
    }

    /**
     * Close any  navs that might be open, then mark this  nav open.
     * Optionally force focus on the first element in the nav (for keyboard nav)
     */
  }, {
    key: "openNav",
    value: function openNav() {
      this.setExpanded('true');
      this.elem.innerText = this.options.closeText;
      this.firstLevel.classList.remove('mobile-hidden');
      // Alert others the nav has opened.
      this.elem.dispatchEvent(this.openEvent);
    }

    /**
     * Mark this  closed, and restore the button text to what it was
     * initially.
     */
  }, {
    key: "closeNav",
    value: function closeNav() {
      this.setExpanded('false');
      this.elem.innerText = this.options.toggleText;
      this.firstLevel.classList.add('mobile-hidden');
      // Alert others the  nav has closed.
      this.elem.dispatchEvent(this.closeEvent);
    }

    /**
     * Set whether or not this is expanded.
     *
     * @param {Boolean} val true for an expanded menu.
     */
  }, {
    key: "setExpanded",
    value: function setExpanded(val) {
      this.elem.setAttribute('aria-expanded', val);
    }

    /**
     * Is this expanded?
     *
     * @return {Boolean}
     *   Returns wether or not the item is expanded.
     */
  }, {
    key: "isExpanded",
    value: function isExpanded() {
      return this.elem.getAttribute('aria-expanded') === 'true';
    }
  }]);
}();

;// ./src/js/components/multi-menu/multi-menu-buttons.js



document.addEventListener('DOMContentLoaded', function (event) {
  multiMenus.forEach(function (nav, index) {
    if (nav.className.match(/su-multi-menu--buttons/)) {
      var theNav = new MultiMenuButtons(nav);
      var toggleElem = nav.querySelector(':scope .su-multi-menu__nav-toggle');
      if (toggleElem) {
        new MobileToggle(toggleElem, theNav);
      }
    }
  });
});
;// ./src/js/components/multi-menu/index.js
// Get'm

;// ./src/js/components/index.js
/**
 * Primary roll up file for all javascript components.
 */

// The Secondary Navigation Component.

// The Mulit Menu Component.

;// ./src/js/base.js
/**
 * @file
 * A Webpack entry file for the theme.
 */

// Include Decanter.
// Right now we import the components as the main decanter.js file always
// imports all of the SASS and we want to make changes to it before we render.


// Import this theme's components.


}();
/******/ })()
;