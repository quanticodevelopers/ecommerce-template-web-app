import {
  LeftToRightBlockQuoteIcon,
  LeftToRightListBulletIcon,
  LeftToRightListNumberIcon,
  Link01Icon,
  RedoIcon,
  TextAlignCenterIcon,
  TextAlignJustifyCenterIcon,
  TextAlignLeft01Icon,
  TextAlignRight01Icon,
  TextBoldIcon,
  TextClearIcon,
  TextItalicIcon,
  TextStrikethroughIcon,
  TextSubscriptIcon,
  TextSuperscriptIcon,
  TextUnderlineIcon,
  UndoIcon,
  Unlink01Icon,
} from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import Placeholder from '@tiptap/extension-placeholder'
import Subscript from '@tiptap/extension-subscript'
import Superscript from '@tiptap/extension-superscript'
import TextAlign from '@tiptap/extension-text-align'
import { EditorContent, useEditor, useEditorState } from '@tiptap/react'
import StarterKit from '@tiptap/starter-kit'
import { useEffect } from 'react'
import type { ReactNode } from 'react'
import { Button } from '@/components/ui/button'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'

type ProductRichTextEditorProps = {
  value: string
  onChange: (value: string) => void
}

type EditorButtonProps = {
  label: string
  active?: boolean
  disabled?: boolean
  onClick: () => void
  children: ReactNode
}

type EditorInstance = NonNullable<ReturnType<typeof useEditor>>

type EditorToolbarProps = {
  editor: EditorInstance | null
}

type BlockType = 'paragraph' | '2' | '3' | '4'

type ToolbarState = {
  blockType: BlockType
  canRedo: boolean
  canUndo: boolean
  isBlockquote: boolean
  isBold: boolean
  isBulletList: boolean
  isCentered: boolean
  isItalic: boolean
  isJustified: boolean
  isLeftAligned: boolean
  isLink: boolean
  isOrderedList: boolean
  isRightAligned: boolean
  isStrike: boolean
  isSubscript: boolean
  isSuperscript: boolean
  isUnderline: boolean
}

const editorContentClassName =
  'min-h-56 px-4 py-3 text-sm outline-none [&_a]:text-primary [&_a]:underline [&_blockquote]:border-l-4 [&_blockquote]:pl-4 [&_blockquote]:text-muted-foreground [&_h2]:text-2xl [&_h2]:font-semibold [&_h3]:text-xl [&_h3]:font-semibold [&_h4]:text-lg [&_h4]:font-semibold [&_ol]:list-decimal [&_ol]:pl-6 [&_p.is-editor-empty:first-child::before]:pointer-events-none [&_p.is-editor-empty:first-child::before]:float-left [&_p.is-editor-empty:first-child::before]:h-0 [&_p.is-editor-empty:first-child::before]:text-muted-foreground [&_p.is-editor-empty:first-child::before]:content-[attr(data-placeholder)] [&_ul]:list-disc [&_ul]:pl-6'

const editorExtensions = [
  StarterKit.configure({
    code: false,
    codeBlock: false,
    heading: {
      levels: [2, 3, 4],
    },
    link: {
      openOnClick: false,
      HTMLAttributes: {
        rel: 'noopener noreferrer nofollow',
        target: '_blank',
      },
    },
  }),
  Placeholder.configure({
    placeholder: 'Descripción completa del producto',
  }),
  Superscript,
  Subscript,
  TextAlign.configure({
    types: ['heading', 'paragraph'],
    alignments: ['left', 'center', 'right', 'justify'],
  }),
]

const emptyToolbarState: ToolbarState = {
  blockType: 'paragraph',
  canRedo: false,
  canUndo: false,
  isBlockquote: false,
  isBold: false,
  isBulletList: false,
  isCentered: false,
  isItalic: false,
  isJustified: false,
  isLeftAligned: true,
  isLink: false,
  isOrderedList: false,
  isRightAligned: false,
  isStrike: false,
  isSubscript: false,
  isSuperscript: false,
  isUnderline: false,
}

function normalizeContent(value: string): string {
  return value === '' ? '<p></p>' : value
}

function EditorButton({ label, active = false, disabled = false, onClick, children }: EditorButtonProps) {
  return (
    <Button
      type="button"
      aria-label={label}
      aria-pressed={active}
      className="size-8"
      disabled={disabled}
      onClick={onClick}
      size="icon-sm"
      variant={active ? 'secondary' : 'ghost'}
    >
      {children}
    </Button>
  )
}

function BlockTypeSelect({ editor, value }: { editor: EditorInstance | null; value: BlockType }) {
  function changeBlockType(blockType: string): void {
    if (editor === null) {
      return
    }

    if (blockType === 'paragraph') {
      editor.chain().focus().setParagraph().run()

      return
    }

    editor
      .chain()
      .focus()
      .setHeading({ level: Number(blockType) as 2 | 3 | 4 })
      .run()
  }

  return (
    <Select
      disabled={editor === null}
      value={value}
      onValueChange={changeBlockType}
    >
      <SelectTrigger
        aria-label="Formato del texto"
        className="h-8 w-30"
        size="sm"
      >
        <SelectValue />
      </SelectTrigger>
      <SelectContent>
        <SelectItem value="paragraph">Párrafo</SelectItem>
        <SelectItem value="2">Título 2</SelectItem>
        <SelectItem value="3">Título 3</SelectItem>
        <SelectItem value="4">Título 4</SelectItem>
      </SelectContent>
    </Select>
  )
}

function EditorToolbar({ editor }: EditorToolbarProps) {
  const state =
    useEditorState<ToolbarState>({
      editor,
      selector: ({ editor: currentEditor }) => {
        if (currentEditor === null) {
          return emptyToolbarState
        }

        const isCentered = currentEditor.isActive({ textAlign: 'center' })
        const isRightAligned = currentEditor.isActive({ textAlign: 'right' })
        const isJustified = currentEditor.isActive({ textAlign: 'justify' })
        const blockType: BlockType = currentEditor.isActive('heading', { level: 2 })
          ? '2'
          : currentEditor.isActive('heading', { level: 3 })
            ? '3'
            : currentEditor.isActive('heading', { level: 4 })
              ? '4'
              : 'paragraph'

        return {
          blockType,
          canRedo: currentEditor.can().chain().redo().run(),
          canUndo: currentEditor.can().chain().undo().run(),
          isBlockquote: currentEditor.isActive('blockquote'),
          isBold: currentEditor.isActive('bold'),
          isBulletList: currentEditor.isActive('bulletList'),
          isCentered,
          isItalic: currentEditor.isActive('italic'),
          isJustified,
          isLeftAligned: !isCentered && !isRightAligned && !isJustified,
          isLink: currentEditor.isActive('link'),
          isOrderedList: currentEditor.isActive('orderedList'),
          isRightAligned,
          isStrike: currentEditor.isActive('strike'),
          isSubscript: currentEditor.isActive('subscript'),
          isSuperscript: currentEditor.isActive('superscript'),
          isUnderline: currentEditor.isActive('underline'),
        }
      },
    }) ?? emptyToolbarState

  function setLink(): void {
    if (editor === null) {
      return
    }

    const previousUrl = editor.getAttributes('link').href as string | undefined
    const url = window.prompt('URL del enlace', previousUrl ?? 'https://')

    if (url === null) {
      return
    }

    if (url.trim() === '') {
      editor.chain().focus().extendMarkRange('link').unsetLink().run()

      return
    }

    editor.chain().focus().extendMarkRange('link').setLink({ href: url.trim() }).run()
  }

  return (
    <div className="flex flex-wrap items-center gap-1 border-b bg-muted/30 p-2">
      <EditorButton
        disabled={!state.canUndo}
        label="Deshacer"
        onClick={() => editor?.chain().focus().undo().run()}
      >
        <HugeiconsIcon
          icon={UndoIcon}
          strokeWidth={1.5}
        />
      </EditorButton>
      <EditorButton
        disabled={!state.canRedo}
        label="Rehacer"
        onClick={() => editor?.chain().focus().redo().run()}
      >
        <HugeiconsIcon
          icon={RedoIcon}
          strokeWidth={1.5}
        />
      </EditorButton>

      <div className="mx-1 h-6 w-px bg-border" />

      <BlockTypeSelect
        editor={editor}
        value={state.blockType}
      />

      <EditorButton
        active={state.isBold}
        label="Negrita"
        onClick={() => editor?.chain().focus().toggleBold().run()}
      >
        <HugeiconsIcon
          icon={TextBoldIcon}
          strokeWidth={1.5}
        />
      </EditorButton>
      <EditorButton
        active={state.isItalic}
        label="Cursiva"
        onClick={() => editor?.chain().focus().toggleItalic().run()}
      >
        <HugeiconsIcon
          icon={TextItalicIcon}
          strokeWidth={1.5}
        />
      </EditorButton>
      <EditorButton
        active={state.isStrike}
        label="Tachado"
        onClick={() => editor?.chain().focus().toggleStrike().run()}
      >
        <HugeiconsIcon
          icon={TextStrikethroughIcon}
          strokeWidth={1.5}
        />
      </EditorButton>
      <EditorButton
        active={state.isUnderline}
        label="Subrayado"
        onClick={() => editor?.chain().focus().toggleUnderline().run()}
      >
        <HugeiconsIcon
          icon={TextUnderlineIcon}
          strokeWidth={1.5}
        />
      </EditorButton>
      <EditorButton
        label="Limpiar formato"
        onClick={() => editor?.chain().focus().unsetAllMarks().clearNodes().run()}
      >
        <HugeiconsIcon
          icon={TextClearIcon}
          strokeWidth={1.5}
        />
      </EditorButton>

      <div className="mx-1 h-6 w-px bg-border" />

      <EditorButton
        active={state.isLink}
        label="Agregar enlace"
        onClick={setLink}
      >
        <HugeiconsIcon
          icon={Link01Icon}
          strokeWidth={1.5}
        />
      </EditorButton>
      <EditorButton
        disabled={!state.isLink}
        label="Quitar enlace"
        onClick={() => editor?.chain().focus().unsetLink().run()}
      >
        <HugeiconsIcon
          icon={Unlink01Icon}
          strokeWidth={1.5}
        />
      </EditorButton>
      <EditorButton
        active={state.isBulletList}
        label="Lista con viñetas"
        onClick={() => editor?.chain().focus().toggleBulletList().run()}
      >
        <HugeiconsIcon
          icon={LeftToRightListBulletIcon}
          strokeWidth={1.5}
        />
      </EditorButton>
      <EditorButton
        active={state.isOrderedList}
        label="Lista numerada"
        onClick={() => editor?.chain().focus().toggleOrderedList().run()}
      >
        <HugeiconsIcon
          icon={LeftToRightListNumberIcon}
          strokeWidth={1.5}
        />
      </EditorButton>
      <EditorButton
        active={state.isBlockquote}
        label="Cita"
        onClick={() => editor?.chain().focus().toggleBlockquote().run()}
      >
        <HugeiconsIcon
          icon={LeftToRightBlockQuoteIcon}
          strokeWidth={1.5}
        />
      </EditorButton>
      <EditorButton
        active={state.isSuperscript}
        label="Superíndice"
        onClick={() => editor?.chain().focus().toggleSuperscript().run()}
      >
        <HugeiconsIcon
          icon={TextSuperscriptIcon}
          strokeWidth={1.5}
        />
      </EditorButton>
      <EditorButton
        active={state.isSubscript}
        label="Subíndice"
        onClick={() => editor?.chain().focus().toggleSubscript().run()}
      >
        <HugeiconsIcon
          icon={TextSubscriptIcon}
          strokeWidth={1.5}
        />
      </EditorButton>

      <div className="mx-1 h-6 w-px bg-border" />

      <EditorButton
        active={state.isLeftAligned}
        label="Alinear a la izquierda"
        onClick={() => editor?.chain().focus().setTextAlign('left').run()}
      >
        <HugeiconsIcon
          icon={TextAlignLeft01Icon}
          strokeWidth={1.5}
        />
      </EditorButton>
      <EditorButton
        active={state.isCentered}
        label="Centrar"
        onClick={() => editor?.chain().focus().setTextAlign('center').run()}
      >
        <HugeiconsIcon
          icon={TextAlignCenterIcon}
          strokeWidth={1.5}
        />
      </EditorButton>
      <EditorButton
        active={state.isRightAligned}
        label="Alinear a la derecha"
        onClick={() => editor?.chain().focus().setTextAlign('right').run()}
      >
        <HugeiconsIcon
          icon={TextAlignRight01Icon}
          strokeWidth={1.5}
        />
      </EditorButton>
      <EditorButton
        active={state.isJustified}
        label="Justificar"
        onClick={() => editor?.chain().focus().setTextAlign('justify').run()}
      >
        <HugeiconsIcon
          icon={TextAlignJustifyCenterIcon}
          strokeWidth={1.5}
        />
      </EditorButton>
    </div>
  )
}

function EditorSurface({ editor, value }: { editor: EditorInstance | null; value: string }) {
  return (
    <div
      aria-busy={editor === null}
      className="overflow-hidden rounded-md border bg-background shadow-xs focus-within:border-ring focus-within:ring-3 focus-within:ring-ring/50"
    >
      <EditorToolbar
        key={editor === null ? 'initial' : 'ready'}
        editor={editor}
      />

      {editor !== null ? (
        <EditorContent editor={editor} />
      ) : value === '' ? (
        <p className={`${editorContentClassName} text-muted-foreground`}>Descripción completa del producto</p>
      ) : (
        <div
          className={editorContentClassName}
          dangerouslySetInnerHTML={{ __html: value }}
        />
      )}
    </div>
  )
}

export default function ProductRichTextEditor({ value, onChange }: ProductRichTextEditorProps) {
  const editor = useEditor({
    immediatelyRender: false,
    shouldRerenderOnTransaction: false,
    extensions: editorExtensions,
    content: normalizeContent(value),
    editorProps: {
      attributes: {
        class: editorContentClassName,
      },
    },
    onUpdate: ({ editor: currentEditor }) => onChange(currentEditor.getHTML()),
  })

  useEffect(() => {
    if (editor === null) {
      return
    }

    const content = normalizeContent(value)

    if (editor.getHTML() !== content) {
      editor.commands.setContent(content, { emitUpdate: false })
    }
  }, [editor, value])

  return (
    <EditorSurface
      editor={editor}
      value={value}
    />
  )
}
