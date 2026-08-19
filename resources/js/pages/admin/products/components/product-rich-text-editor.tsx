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
import Placeholder from '@tiptap/extension-placeholder'
import Subscript from '@tiptap/extension-subscript'
import Superscript from '@tiptap/extension-superscript'
import TextAlign from '@tiptap/extension-text-align'
import { EditorContent, useEditor } from '@tiptap/react'
import StarterKit from '@tiptap/starter-kit'
import type { ReactNode } from 'react'
import { Button } from '@/components/ui/button'
import { Icon } from '@/components/ui/icon'
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

export default function ProductRichTextEditor({ value, onChange }: ProductRichTextEditorProps) {
  const editor = useEditor({
    immediatelyRender: false,
    extensions: [
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
    ],
    content: value,
    editorProps: {
      attributes: {
        class:
          'min-h-56 px-4 py-3 text-sm outline-none [&_a]:text-primary [&_a]:underline [&_blockquote]:border-l-4 [&_blockquote]:pl-4 [&_blockquote]:text-muted-foreground [&_h2]:text-2xl [&_h2]:font-semibold [&_h3]:text-xl [&_h3]:font-semibold [&_h4]:text-lg [&_h4]:font-semibold [&_ol]:list-decimal [&_ol]:pl-6 [&_p.is-editor-empty:first-child::before]:pointer-events-none [&_p.is-editor-empty:first-child::before]:float-left [&_p.is-editor-empty:first-child::before]:h-0 [&_p.is-editor-empty:first-child::before]:text-muted-foreground [&_p.is-editor-empty:first-child::before]:content-[attr(data-placeholder)] [&_ul]:list-disc [&_ul]:pl-6',
      },
    },
    onUpdate: ({ editor: currentEditor }) => onChange(currentEditor.getHTML()),
  })

  if (editor === null) {
    return <div className="min-h-66 animate-pulse rounded-md border bg-muted/30" />
  }

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
    <div className="overflow-hidden rounded-md border bg-background shadow-xs focus-within:border-ring focus-within:ring-3 focus-within:ring-ring/50">
      <div className="flex flex-wrap items-center gap-1 border-b bg-muted/30 p-2">
        <EditorButton
          disabled={!editor.can().chain().focus().undo().run()}
          label="Deshacer"
          onClick={() => editor.chain().focus().undo().run()}
        >
          <Icon iconNode={UndoIcon} />
        </EditorButton>
        <EditorButton
          disabled={!editor.can().chain().focus().redo().run()}
          label="Rehacer"
          onClick={() => editor.chain().focus().redo().run()}
        >
          <Icon iconNode={RedoIcon} />
        </EditorButton>

        <div className="mx-1 h-6 w-px bg-border" />

        <Select
          value={editor.isActive('heading') ? String(editor.getAttributes('heading').level) : 'paragraph'}
          onValueChange={(heading) => {
            if (heading === 'paragraph') {
              editor.chain().focus().setParagraph().run()

              return
            }

            editor
              .chain()
              .focus()
              .toggleHeading({ level: Number(heading) as 2 | 3 | 4 })
              .run()
          }}
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

        <EditorButton
          active={editor.isActive('bold')}
          label="Negrita"
          onClick={() => editor.chain().focus().toggleBold().run()}
        >
          <Icon iconNode={TextBoldIcon} />
        </EditorButton>
        <EditorButton
          active={editor.isActive('italic')}
          label="Cursiva"
          onClick={() => editor.chain().focus().toggleItalic().run()}
        >
          <Icon iconNode={TextItalicIcon} />
        </EditorButton>
        <EditorButton
          active={editor.isActive('strike')}
          label="Tachado"
          onClick={() => editor.chain().focus().toggleStrike().run()}
        >
          <Icon iconNode={TextStrikethroughIcon} />
        </EditorButton>
        <EditorButton
          active={editor.isActive('underline')}
          label="Subrayado"
          onClick={() => editor.chain().focus().toggleUnderline().run()}
        >
          <Icon iconNode={TextUnderlineIcon} />
        </EditorButton>
        <EditorButton
          label="Limpiar formato"
          onClick={() => editor.chain().focus().unsetAllMarks().clearNodes().run()}
        >
          <Icon iconNode={TextClearIcon} />
        </EditorButton>

        <div className="mx-1 h-6 w-px bg-border" />

        <EditorButton
          active={editor.isActive('link')}
          label="Agregar enlace"
          onClick={setLink}
        >
          <Icon iconNode={Link01Icon} />
        </EditorButton>
        <EditorButton
          disabled={!editor.isActive('link')}
          label="Quitar enlace"
          onClick={() => editor.chain().focus().unsetLink().run()}
        >
          <Icon iconNode={Unlink01Icon} />
        </EditorButton>
        <EditorButton
          active={editor.isActive('bulletList')}
          label="Lista con viñetas"
          onClick={() => editor.chain().focus().toggleBulletList().run()}
        >
          <Icon iconNode={LeftToRightListBulletIcon} />
        </EditorButton>
        <EditorButton
          active={editor.isActive('orderedList')}
          label="Lista numerada"
          onClick={() => editor.chain().focus().toggleOrderedList().run()}
        >
          <Icon iconNode={LeftToRightListNumberIcon} />
        </EditorButton>
        <EditorButton
          active={editor.isActive('blockquote')}
          label="Cita"
          onClick={() => editor.chain().focus().toggleBlockquote().run()}
        >
          <Icon iconNode={LeftToRightBlockQuoteIcon} />
        </EditorButton>
        <EditorButton
          active={editor.isActive('superscript')}
          label="Superíndice"
          onClick={() => editor.chain().focus().toggleSuperscript().run()}
        >
          <Icon iconNode={TextSuperscriptIcon} />
        </EditorButton>
        <EditorButton
          active={editor.isActive('subscript')}
          label="Subíndice"
          onClick={() => editor.chain().focus().toggleSubscript().run()}
        >
          <Icon iconNode={TextSubscriptIcon} />
        </EditorButton>

        <div className="mx-1 h-6 w-px bg-border" />

        <EditorButton
          active={editor.isActive({ textAlign: 'left' })}
          label="Alinear a la izquierda"
          onClick={() => editor.chain().focus().setTextAlign('left').run()}
        >
          <Icon iconNode={TextAlignLeft01Icon} />
        </EditorButton>
        <EditorButton
          active={editor.isActive({ textAlign: 'center' })}
          label="Centrar"
          onClick={() => editor.chain().focus().setTextAlign('center').run()}
        >
          <Icon iconNode={TextAlignCenterIcon} />
        </EditorButton>
        <EditorButton
          active={editor.isActive({ textAlign: 'right' })}
          label="Alinear a la derecha"
          onClick={() => editor.chain().focus().setTextAlign('right').run()}
        >
          <Icon iconNode={TextAlignRight01Icon} />
        </EditorButton>
        <EditorButton
          active={editor.isActive({ textAlign: 'justify' })}
          label="Justificar"
          onClick={() => editor.chain().focus().setTextAlign('justify').run()}
        >
          <Icon iconNode={TextAlignJustifyCenterIcon} />
        </EditorButton>
      </div>

      <EditorContent editor={editor} />
    </div>
  )
}
